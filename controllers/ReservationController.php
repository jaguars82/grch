<?php

namespace app\controllers;

use app\models\Flat;
use app\models\Application;
use app\models\form\ApplicationForm;
use app\models\ApplicationHistory;
use app\models\form\ApplicationHistoryForm;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\components\SharedDataFilter;
use tebe\inertia\web\Controller;
use yii\helpers\ArrayHelper;

class ReservationController extends Controller
{
    public function behaviors()
    {
        return [ 
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['make'],
                        'roles' => ['admin', 'agent', 'manager'],
                    ],
                ]
            ],
            [
                'class' => SharedDataFilter::class
            ]
        ];
    }

    /**
     * @param type $flatId
     */
    public function actionMake($flatId)
    {
        $model = Flat::find()
            ->where(['id' => $flatId])
            ->one();
        
        $flat = ArrayHelper::toArray($model);
        $flat['newbuilding'] = ArrayHelper::toArray($model->newbuilding);
        $flat['newbuildingComplex'] = ArrayHelper::toArray($model->newbuildingComplex);

        if (\Yii::$app->request->isPost) {
            $applicationForm = new ApplicationForm();
            $applicationForm->load(\Yii::$app->request->post(), '');
            // echo '<pre>'; var_dump(\Yii::$app->request->post()); echo '</pre>'; die();
            echo '<pre>'; var_dump($applicationForm); echo '</pre>'; die();
        }

        return $this->inertia('Reservation/Make', [
            'flat' => $flat,
        ]);
    }
}