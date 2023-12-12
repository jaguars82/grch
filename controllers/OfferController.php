<?php

namespace app\controllers;

use app\components\exceptions\AppException;
use app\components\traits\CustomRedirects;
use app\components\SharedDataFilter;
use app\models\Flat;
use app\models\Offer;
use app\models\search\OfferSearch;
use kartik\mpdf\Pdf;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * OfferController implements the CRUD actions for Offer model.
 */
class OfferController extends Controller
{
    use CustomRedirects;
    
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'update', 'delete'],
                        'roles' => ['admin', 'manager', 'agent'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['make', 'send-email', 'telegram', 'download-pdf'],
                        'roles' => ['admin', 'manager', 'agent'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['view'],
                        'roles' => ['?', '@'],
                    ],
                ]
            ],
            [
                'class' => SharedDataFilter::class
            ],
        ];
    }

    /**
     * Lists all Offer models.
     * 
     * @return mixed
     */
    public function actionIndex()
    {        
        $searchModel = new OfferSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'itemsCount' => $searchModel->itemsCount,
        ]);
    }

    /**
     * Displays a single Offer model.
     * 
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {        
        if (($model = Offer::find()->where(['url' => $id])->one()) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }
        
        $model->touch('visited_at');
        
        return $this->renderPartial('view', [
            'model' => $model,
        ]); 
    }

    /**
     * Updates an existing Offer model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * 
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        if (\Yii::$app->request->isPost) {
            try {
                $data = \Yii::$app->request->post();
                $model->fill($data, ['settings', 'user_id', 'flat_id']);
        
                if (isset($data['settings'])) {
                    $model->settings = json_encode($data['settings']);
                } else {
                    $model->settings = NULL;
                }
                
                if (!$model->save()) {
                    throw new \Exception();
                }
            } catch (\Exception $e) {
                return $this->redirectBackWhenException($e);
            }

            return $this->redirectWithSuccess(['update', 'id' => $model->id], 'КП обновлен');
        }
        
        return $this->render('make', [
            'flat' => $model->flat,
            'offer' => $model,
        ]);
    }

    /**
     * Deletes an existing Offer model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * 
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirectWithSuccess(['index'], 'КП удален');
    }
    
    /**
     * Make offer for given flat
     * 
     * @param type $flatId
     * @return type
     * @throws NotFoundHttpException
     */
    public function actionMake($flatId)
    {
        try {
            if (($flat = Flat::findOne($flatId)) === null) {
                throw new NotFoundHttpException('Данные отсутсвуют');
            }

            if ($flat->isSold()) {
                throw new AppException('Для проданной квартиры нельзя сформировать КП');           
            }
            
            if (\Yii::$app->request->isPost) {
                $data = array_merge(\Yii::$app->request->post(), [
                    'user_id' => \Yii::$app->user->id,
                    'flat_id' => $flat->id,
                    'url' => substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 8),
                ]);
                $model = new Offer();
                $model->fill($data, ['settings', 'new_price']);

                if (isset($data['settings'])) {
                    $model->settings = json_encode($data['settings']);
                }

                if ($flat->newbuildingComplex->offer_new_price_permit && isset($data['new_price_cash']) && !empty($data['new_price_cash'])) {
                    $model->new_price_cash = $data['new_price_cash'];
                } else {
                    $model->new_price_cash = $flat->cashPriceWithDiscount;
                }
                
                if ($flat->newbuildingComplex->offer_new_price_permit && isset($data['new_price_credit']) && !empty($data['new_price_credit'])) {
                    $model->new_price_credit = $data['new_price_credit'];
                } elseif(!is_null($flat->price_credit)) {
                    $model->new_price_credit = $flat->creditPriceWithDiscount;
                }
                
                if (!$model->save()) {
                    throw new \Exception();
                }
                
                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;        
                \Yii::$app->response->data = [
                    'link' => \Yii::$app->request->hostInfo . Url::to(['offer/view', 'id' => $model->url])
                ];
                
                return;
            }
        } catch (\Exception $e) {
            return $this->redirectBackWhenException($e);
        }
        
        return $this->render('make', [
            'flat' => $flat,
        ]);
    }
  
    /**
     * Send email with offer pdf file for given flat
     * 
     * @param type $flatId
     * @return type
     * @throws NotFoundHttpException
     */
    public function actionSendEmail($flatId)
    {
        try {
            if (($flat = Flat::findOne($flatId)) === null) {
                throw new NotFoundHttpException('Данные отсутсвуют');
            }
            
            $filename = $this->getPdfFile($flat);
            
            if (isset(\Yii::$app->request->get()['new_price_cash'])) {
                $newPriceCash = \Yii::$app->request->get()['new_price_cash'];
            } else {
                $newPriceCash = NULL;
            }

            if (isset(\Yii::$app->request->get()['new_price_credit'])) {
                $newPriceCredit = \Yii::$app->request->get()['new_price_credit'];
            } else {
                $newPriceCredit = NULL;
            }

            $sendemail = \Yii::$app->mailer->compose('offer', ['flat' => $flat, 'newPriceCash' => $newPriceCash, 'newPriceCredit' => $newPriceCredit])
                ->attach($filename)
                ->setFrom([\Yii::$app->params['senderEmail'] => \Yii::$app->params['senderName']])
                ->setTo(\Yii::$app->user->identity->email)
                ->setSubject($this->getOfferName($flat))
                ->send();

            unlink($filename);
        } catch (\Exception $e) {
            return $this->redirectBackWhenException($e);
        }
                  
        return $this->redirectWithSuccess(['make', 'flatId' => $flat->id], 'Коммерческое предложение отправлено на почту');
    }
    
    /**
     * Send message with offer pdf file to telegram for given flat
     * 
     * @param type $flatId
     * @return type
     * @throws NotFoundHttpException
     */
    public function actionTelegram($flatId)
    {
        try {
            if (is_null(($telegramChatId = \Yii::$app->user->identity->telegram_chat_id)) || empty($telegramChatId)) {
                throw new AppException('У вас не установлен Telegram Chat ID. Установите его и попробуйте еще раз.');
            }

            if (($flat = Flat::findOne($flatId)) === null) {
                throw new NotFoundHttpException('Данные отсутсвуют');
            }
            
            $filename = $this->getPdfFile($flat);
            
            if (isset(\Yii::$app->request->get()['new_price_cash'])) {
                $newPriceCash = \Yii::$app->request->get()['new_price_cash'];
            } else {
                $newPriceCash = NULL;
            }

            if (isset(\Yii::$app->request->get()['new_price_credit'])) {
                $newPriceCredit = \Yii::$app->request->get()['new_price_credit'];
            } else {
                $newPriceCredit = NULL;
            }

            $bot = new \TelegramBot\Api\BotApi(\Yii::$app->params['telegramApiKey']);
            $bot->setCurlOption(CURLOPT_HTTPHEADER, array('Expect:'));
            $bot->setCurlOption(CURLOPT_TIMEOUT, 10);
            $bot->sendDocument($telegramChatId, new \CURLFile($filename), $this->renderPartial('telegram', ['flat' => $flat, 'newPriceCash' => $newPriceCash, 'newPriceCredit' => $newPriceCredit]));

            unlink($filename);
        } catch (\Exception $e) {
            return $this->redirectBackWhenException($e);
        }
                  
        return $this->redirectWithSuccess(['make', 'flatId' => $flat->id], 'Коммерческое предложение отправлено на Telegram');
    }
    
    /**
     * Download offer pdf file for given flat
     * 
     * @param type $flatId
     * @return type
     * @throws NotFoundHttpException
     */
    public function actionDownloadPdf($flatId)
    {
        error_reporting(0);
        try {
            \Yii::$app->getModule('debug')->instance->allowedIPs = [];

            if (($flat = Flat::findOne($flatId)) === null) {
                throw new NotFoundHttpException('Данные отсутсвуют');
            }

            $pdf = $this->getPdfFile($flat, true);
            $result = $pdf->render();
        } catch (\Exception $e) {
            return $this->redirectBackWhenException($e);
        }
        
        return $result;
    }

    /**
     * Finds the Offer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * 
     * @param integer $id
     * @return Offer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {        
        if (($model = Offer::findOne($id)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }

        return $model;
    }
    
    /**
     * Get offer as pdf file for given flat
     * 
     * @param type $flat
     * @return Pdf
     * @throws NotFoundHttpException
     */
    private function getPdfFile($flat, $isReturnFile = false)
    {        
        ini_set('pcre.backtrack_limit', 30000000);
        
        if (isset(\Yii::$app->request->get()['settings'])) {
            $settings = \Yii::$app->request->get()['settings'];
        } else {
            $settings = [];
        }
        
        if (isset(\Yii::$app->request->get()['new_price_cash'])) {
            $newPriceCash = \Yii::$app->request->get()['new_price_cash'];
        } else {
            $newPriceCash = NULL;
        }
        
        if (isset(\Yii::$app->request->get()['new_price_credit'])) {
            $newPriceCredit = \Yii::$app->request->get()['new_price_credit'];
        } else {
            $newPriceCredit = NULL;
        }
        
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $this->renderPartial('pdf', ['flat' => $flat, 'settings' => $settings, 'newPriceCash' => $newPriceCash, 'newPriceCredit' => $newPriceCredit]),
            'filename' => $this->getOfferName($flat) . '.pdf',
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
        
        if ($isReturnFile) {
            return $pdf;
        }
        
        $filename = \Yii::getAlias('@webroot/uploads')."/{$pdf->filename}";
        $pdf->Output($pdf->content, $filename, \Mpdf\Output\Destination::FILE);
        
        return $filename;
    }
    
    /**
     * Get offer name for pdf filename, email subject and etc.
     * @param type $flat
     * @return type
     */
    private function getOfferName($flat)
    {
        return "Коммерческое предложение - {$flat->newbuildingComplex->name}, {$flat->newbuilding->name}, №" . $flat->number; 
    }
}
