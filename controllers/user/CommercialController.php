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
                    'view' => ['GET', 'POST']
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['make', 'index', 'view'],
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

        $commercials = '';

        if (\Yii::$app->request->isPost) {
            
            $commercialForm = new CommercialForm();

            $transaction = \Yii::$app->db->beginTransaction();

            try {
                $commercialForm->load(\Yii::$app->request->post(), '');
                $commercialForm->initiator_id = \Yii::$app->user->id;
                $commercialForm->active = 1;
                $commercialForm->is_formed = 0;
                $commercialForm->number = 'N1';
                $commercialModel = (new Commercial())->fill($commercialForm->attributes);
                $commercialModel->save();

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
        $model = new Commercial();

        $commercials = '';

        return $this->inertia('User/Commercial/Index', [
            'user' => \Yii::$app->user->identity,
            'commercials' => ArrayHelper::toArray($commercials),
        ]);
    }

    public function actionView($id)
    {
        $commercialModel =  (new Commercial())->findOne($id);

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
            $flatItem['newbuilding'] = ArrayHelper::toArray($flat->newbuilding);
            array_push($flatsArray, $flatItem);
        }

        $commercialMode = count($flatsArray) > 1 ? 'multiple' : 'single';
       
        return $this->inertia('User/Commercial/View', [
            'commercial' => $commercialArray,
            'flats' => $flatsArray,
            'commercialMode' => $commercialMode,
        ]);
    }
}