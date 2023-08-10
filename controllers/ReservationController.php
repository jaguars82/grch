<?php

namespace app\controllers;

use app\models\Flat;
use app\models\AuthAssignment;
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
        $flat['developer'] = ArrayHelper::toArray($model->developer);
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

                $flat = Flat::findOne($applicationModel->flat_id);
                $flat->is_applicated = $applicationForm->attributes['self_reservation'] === true ? Flat::APPLICATED_USER : Flat::APPLICATED_AGREGATOR;
                $flat->is_reserved = $applicationForm->attributes['self_reservation'] === true ? 1 : 0;
                $flat->save();

                $applicationHistoryForm->application_id = $applicationModel->id;
                $applicationHistoryForm->user_id = $applicationModel->applicant_id;
                $applicationHistoryForm->action = $applicationForm->attributes['self_reservation'] === true ? Application::STATUS_SELF_RESERVED : Application::STATUS_RESERV_APPLICATED;
                $applicationHistoryForm->comment = $applicationModel->applicant_comment;
                $applicationHistoryModel = (new ApplicationHistory())->fill($applicationHistoryForm->attributes);
                $applicationHistoryModel->save();

                // message body text
                $messageText = '';
                $clientFirstName = !empty($applicationModel->client_firstname) ? $applicationModel->client_firstname : '';
                $clientLastName = !empty($applicationModel->client_lastname) ? $applicationModel->client_lastname : '';
                $clientMiddleName = !empty($applicationModel->client_middlename) ? $applicationModel->client_middlename : '';
                if ($applicationForm->attributes['self_reservation'] === true) {
                    $messageText .= '<span style="text-transform: uppercase;"><strong>Самостоятельная бронь</strong></span><br />';
                }
                if (!empty($clientFirstName) || !empty($clientLastName) || !empty($clientMiddleName)) {
                    $messageText .= 'Клиент: '.$clientFirstName.' '.$clientMiddleName.' '.$clientLastName.'<br />';
                }
                if (!empty($applicationModel->client_phone)) {
                    $messageText .= 'Телефон клиента: '.$applicationModel->client_phone.'<br />';
                }
                if (!empty($applicationModel->client_email)) {
                    $messageText .= 'Email клиента: '.$applicationModel->client_email.'<br />';
                }
                if (!empty($applicationModel->applicant_comment)) {
                    $messageText .= 'Комментарий к заявке: '.$applicationModel->applicant_comment.'<br />';
                }

                $notificationForm->initiator_id = $applicationModel->applicant_id;
                $notificationForm->type = 2;
                $notificationForm->recipient_group = 'admin';
                $notificationForm->topic = 'Новая заявка на бронирование '.$applicationModel->application_number;
                $notificationForm->body = $messageText.'Для просмотра подробностей перейдите на страницу заявки';
                $notificationForm->action_text = 'Перейти';
                $notificationForm->action_url = '/user/application/view?id='.$applicationModel->id;
                $notificationModel = (new Notification())->fill($notificationForm->attributes);              
                $notificationModel->save();

                $transaction->commit();

                /** Send email-notifications for admins */
                $admins = (new AuthAssignment())->admins;

                foreach ($admins as $admin) {
                    if (!empty ($admin)) {
                        \Yii::$app->mailer->compose()
                        ->setTo($admin->email)
                        ->setFrom([\Yii::$app->params['senderEmail'] => \Yii::$app->params['senderName']])
                        ->setSubject('Поступила новая заявка на бронирование')
                        ->setHtmlBody($messageText."Ссылка для просмотра заявки: https://grch.ru/user/application/view?id=$applicationModel->id")
                        ->send(); 
                    }
                }

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