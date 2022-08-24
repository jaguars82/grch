<?php

namespace app\controllers;

use app\models\Flat;
use app\models\Application;
use app\models\form\ApplicationForm;
use app\models\ApplicationHistory;
use app\models\form\ApplicationHistoryForm;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\components\traits\CustomRedirects;
use app\components\SharedDataFilter;
use tebe\inertia\web\Controller;
use yii\helpers\ArrayHelper;

class ReservationController extends Controller
{
    use CustomRedirects;

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
            $applicationHistoryForm = new ApplicationHistoryForm();

            $transaction = \Yii::$app->db->beginTransaction();

            try {
                $applicationForm->load(\Yii::$app->request->post(), '');
                $applicationModel = (new Application())->fill($applicationForm->attributes);
                $applicationModel->save();

                $applicationHistoryForm->application_id = $applicationModel->id;
                $applicationHistoryForm->user_id = $applicationModel->applicant_id;
                $applicationHistoryForm->action = Application::STATUS_RESERV_APPLICATED;
                $applicationHistoryForm->comment = $applicationModel->applicant_comment;
                $applicationHistoryModel = (new ApplicationHistory())->fill($applicationHistoryForm->attributes);
                $applicationHistoryModel->save();

                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                return $this->redirect(['make', 'flatId' => $model->id, 'status' => 'err']);
            }

            return $this->redirect(['make', 'flatId' => $model->id, 'res' => 'ok']);
        }

        return $this->inertia('Reservation/Make', [
            'flat' => $flat,
            'result' => \Yii::$app->request->get('res')
        ]);
    }
}