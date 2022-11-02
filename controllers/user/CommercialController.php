<?php

namespace app\controllers\user;

use app\components\traits\CustomRedirects;
use app\components\SharedDataFilter;
use app\models\Flat;
use app\models\Commercial;
use app\models\CommercialFlat;
use app\models\CommercialHistory;
use app\models\form\CommercialForm;
use app\models\form\CommercialHistoryForm;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use tebe\inertia\web\Controller;
use yii\helpers\ArrayHelper;
use kartik\mpdf\Pdf;
use yii\web\NotFoundHttpException;

class CommercialController extends Controller
{
    use CustomRedirects;

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'make' => ['GET', 'POST'],
                    'index' => ['GET', 'POST'],
                    'view' => ['GET', 'POST'],
                    'download-pdf' => ['GET', 'POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['make', 'index', 'view', 'download-pdf'],
                        'roles' => ['admin', 'manager', 'agent', 'developer_repres'],
                    ],
                ]
            ],
            [
                'class' => SharedDataFilter::class
            ],
        ];
    }

    public function actionMake($flatId) {

        $model = Flat::find()
        ->where(['id' => $flatId])
        ->one();
    
        $flat = ArrayHelper::toArray($model);
        $flat['newbuilding'] = ArrayHelper::toArray($model->newbuilding);
        $flat['newbuildingComplex'] = ArrayHelper::toArray($model->newbuildingComplex);

        $commercials = (new Commercial())->editableCommercials;

        if (\Yii::$app->request->isPost) {
            
            $commercialForm = new CommercialForm();

            $transaction = \Yii::$app->db->beginTransaction();

            try {

                if (\Yii::$app->request->post('mode') === 'new') {
                    $commertialsAmount = (new Commercial())->find()
                        ->where(['initiator_id' => \Yii::$app->user->id])
                        ->count();
                    $newCommercialNumber = $commertialsAmount + 1;
                    $commercialForm->load(\Yii::$app->request->post(), '');
                    $commercialForm->initiator_id = \Yii::$app->user->id;
                    $commercialForm->active = 1;
                    $commercialForm->is_formed = 0;
                    $commercialForm->number = $newCommercialNumber.'-'.\Yii::$app->user->id;
                    $commercialModel = (new Commercial())->fill($commercialForm->attributes);
                    $commercialModel->save();
                } else {
                    $commercialModel =  $this->findModel(\Yii::$app->request->post('commercialId'));
                }

                $commercialModel->link('flats', $model);

                $transaction->commit();

            } catch (\Exception $e) {
                $transaction->rollBack();
                return $this->redirect(['make', 'flatId' => $flatId, 'res' => 'err']);
            }

            return $this->redirect(['view', 'id' => $commercialModel->id]);
        }

        return $this->inertia('User/Commercial/Make', [
            'user' => \Yii::$app->user->identity,
            'flat' => $flat,
            'commercials' => ArrayHelper::toArray($commercials),
        ]);
    }

    public function actionIndex()
    {

        $commercials = (new Commercial())->find()
            ->where(['initiator_id' => \Yii::$app->user->id])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        $commercialsArray = array();
        foreach ($commercials as $commercial) {
            $commercialItem = ArrayHelper::toArray($commercial);
            $flatsArray = array();
            foreach ($commercial->flats as $flat) {
                $flatItem = ArrayHelper::toArray($flat);
                $flatItem['newbuildingComplex'] = ArrayHelper::toArray($flat->newbuilding->newbuildingComplex);
                $flatItem['newbuilding'] = ArrayHelper::toArray($flat->newbuilding);
                array_push($flatsArray, $flatItem);
            }
            $commercialItem['flats'] = $flatsArray;
            array_push($commercialsArray, $commercialItem);
        }

        return $this->inertia('User/Commercial/Index', [
            'user' => \Yii::$app->user->identity,
            'commercials' => $commercialsArray,
        ]);
    }

    public function actionView($id)
    {
        $commercialModel =  $this->findModel($id);

        $commercialArray = ArrayHelper::toArray($commercialModel);
        $commercialArray['initiator'] = ArrayHelper::toArray($commercialModel->initiator);
        $commercialArray['initiator']['organization'] = ArrayHelper::toArray($commercialModel->initiator->agency);

        $flats = $commercialModel->flats;
        $flatsArray = array();
        foreach ($flats as $flat) {
            $flatItem = ArrayHelper::toArray($flat);
            $flatItem['floorLayoutImage'] = !is_null($flat->floorLayoutSvg) ? $flat->floorLayoutSvg : NULL;
            $flatItem['developer'] = ArrayHelper::toArray($flat->developer);
            $flatItem['newbuildingComplex'] = ArrayHelper::toArray($flat->newbuilding->newbuildingComplex);
            $flatItem['newbuildingComplex']['address'] = $flat->newbuilding->newbuildingComplex->address;
            foreach ($flat->furnishes as $key => $furnish) {
                $finishing = ArrayHelper::toArray($furnish);
                $finishing['furnishImages'] = $furnish->furnishImages;
                $flatItem['finishing'][] = ArrayHelper::toArray($finishing);
            }
            $flatItem['newbuilding'] = ArrayHelper::toArray($flat->newbuilding);
            $flatItem['advantages'] = ArrayHelper::toArray($flat->newbuilding->newbuildingComplex->advantages);
            array_push($flatsArray, $flatItem);
        }

        $commercialMode = count($flatsArray) > 1 ? 'multiple' : 'single';

        $viewOptions = [
            'commercial' => $commercialArray,
            'flats' => $flatsArray,
            'commercialMode' => $commercialMode,
        ];
        
        /** Commercial operations */
        if(\Yii::$app->request->isPost)  {
            switch(\Yii::$app->request->post('operation')) {
                /** Generate PDF-file */
                case 'pdf':
                    $this->downloadPdf($commercialModel->id);
                    $viewOptions = array_merge($viewOptions, [
                        'id' => $commercialModel->id,
                        'operation' => 'pdf',
                        'status' => 'ok'
                    ]);
                    break;
                    /** Ghange commercial settings */
                    case 'settings':
                        //echo '<pre>'; var_dump(\Yii::$app->request->post('settings')); echo '</pre>'; die;
                        $commercialModel->settings = json_encode(\Yii::$app->request->post('settings'));
                        $commercialModel->save();
                        break;
            }
        }      
        
        return $this->inertia('User/Commercial/View', $viewOptions);
    }

    /**
     * Download offer pdf file for given flat
     * 
     * @param type $flatId
     * @return type
     * @throws NotFoundHttpException
     */
    // public function actionDownloadPdf($flatId)
    private function downloadPdf($commercialId)
    {
        error_reporting(0);
        try {
            \Yii::$app->getModule('debug')->instance->allowedIPs = [];

            if (($commercial = Commercial::findOne($commercialId)) === null) {
                throw new NotFoundHttpException('Данные отсутсвуют');
            }

            $pdf = $this->getPdfFile($commercial, true);
            //$result = $pdf->render();
        } catch (\Exception $e) {
            echo '<pre>'; var_dump($e); echo '</pre>';
            return $this->redirectBackWhenException($e);
        }
        //echo '<pre>'; var_dump($result); echo '</pre>'; die();
        //return \Yii::$app->response->sendFile('/uploads/Коммерческое предложение - 10.pdf');

        /*return $this->redirect([
            'view',
            'id' => 2,
            'operation' => 'pdf',
            'status' => 'ok'
        ]);*/

        //return $result;
    }

    /**
     * Get commercial as pdf file
     * 
     * @param type $flat
     * @return Pdf
     * @throws NotFoundHttpException
     */
    private function getPdfFile($commercial, $isReturnFile = false)
    {        
        ini_set('pcre.backtrack_limit', 30000000);
        
        if (isset(\Yii::$app->request->get()['settings'])) {
            $settings = \Yii::$app->request->get()['settings'];
        } else {
            $settings = [];
        }
        
        /* if (isset(\Yii::$app->request->get()['new_price_cash'])) {
            $newPriceCash = \Yii::$app->request->get()['new_price_cash'];
        } else {
            $newPriceCash = NULL;
        } */
        
        /* if (isset(\Yii::$app->request->get()['new_price_credit'])) {
            $newPriceCredit = \Yii::$app->request->get()['new_price_credit'];
        } else {
            $newPriceCredit = NULL;
        } */
        
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
        'content' => $this->renderPartial('pdf', ['commercial' => $commercial, 'settings' => $settings]),
            'filename' => $this->getOfferName($commercial->number) . '.pdf',
            'cssFile' => [
                '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
                '@webroot/css/offer.css'
            ],
            'marginBottom' => 0,
            'marginTop' => 0,
            'marginLeft' => 0,
            'marginRight' => 0,
            'cssInline' => 'body {margin: 0; padding: 0}',
            'options' => [],
            'methods' => [],
        ]);
        
        /*if ($isReturnFile) {
            return $pdf;
        }*/
        
        $filename = \Yii::getAlias('@webroot/downloads')."/{$pdf->filename}";
        $pdf->Output($pdf->content, $filename, \Mpdf\Output\Destination::FILE);
        
        return $filename;
    }

    /**
     * Get commercial name for pdf filename, email subject and etc.
     * @param type $flat
     * @return type
     */
    private function getOfferName($number)
    {
        return "Коммерческое предложение - ". $number; 
    }

    /**
     * Finds the Commercial model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * 
     * @param integer $id
     * @return Commercial the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Commercial::findOne($id)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }

        return $model;
    }
}