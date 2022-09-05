<?php

namespace app\controllers;

use app\models\Flat;
use app\models\Application;
use app\models\form\ApplicationForm;
use app\models\ApplicationHistory;
use app\models\form\ApplicationHistoryForm;
use app\models\Notification;
use app\models\form\NotificationForm;
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
            $notificationForm = new NotificationForm();

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

                $notificationForm->initiator_id = $applicationModel->applicant_id;
                $notificationForm->type = 2;
                $notificationForm->recipient_group = 'admin';
                $notificationForm->topic = 'Новая заявка на бронирование '.$applicationModel->application_number;
                $notificationForm->body = 'Для просмотра подробностей перейдите на страницу заявки';
                $notificationForm->action_text = 'Перейти';
                $notificationForm->action_url = '/user/application/view?id='.$applicationModel->id;
                $notificationModel = (new Notification())->fill($notificationForm->attributes);              
                $notificationModel->save();

                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                return $this->redirect(['make', 'flatId' => $model->id, 'res' => 'err']);
            }

            return $this->redirect(['make', 'flatId' => $model->id, 'res' => 'ok', 'appId' => $applicationModel->id]);
        }

        return $this->inertia('Reservation/Make', [
            'flat' => $flat,
            'applicationsAmount' => (new Application)->getApplicationsByAuthor(\Yii::$app->user->id)->count(),
            'result' => \Yii::$app->request->get('res'),
            'appId' => \Yii::$app->request->get('appId'),
        ]);
    }
}