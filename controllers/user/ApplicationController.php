<?php

namespace app\controllers\user;

use app\components\traits\CustomRedirects;
use app\components\SharedDataFilter;
use app\models\User;
use app\models\Flat;
use app\models\Application;
use app\models\form\ApplicationForm;
use app\models\ApplicationHistory;
use app\models\form\ApplicationHistoryForm;
use app\models\Notification;
use app\models\form\NotificationForm;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use tebe\inertia\web\Controller;
use yii\helpers\ArrayHelper;

class ApplicationController extends Controller
{
    use CustomRedirects;

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['GET', 'POST'],
                    'view' => ['GET', 'POST']
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'roles' => ['admin', 'manager', 'agent', 'developer_repres'],
                    ],
                ]
            ],
            [
                'class' => SharedDataFilter::class
            ],
        ];
    }

    public function actionIndex()
    {
        $model = new Application();

        if (\Yii::$app->user->can('admin')) {
            $applications = $model->activeApplications;    
        } elseif (\Yii::$app->user->can('developer_repres')) {
            $applications = $model->getApplicationsForDeveloper(\Yii::$app->user->developer_id)->all();
        } elseif (\Yii::$app->user->can('agent') || \Yii::$app->user->can('manager')) {
            $applications = $model->getApplicationsByAuthor(\Yii::$app->user->id)->all();
        }
        
        return $this->inertia('User/Application/Index', [
            'user' => \Yii::$app->user->identity,
            'applications' => ArrayHelper::toArray($applications),
            'statusMap' => Application::$status
        ]);
    }

    public function actionView($id)
    {
        $application = (new Application())->findOne($id);
        $flat = ArrayHelper::toArray($application->flat);
        $flat['newbuilding'] = ArrayHelper::toArray($application->flat->newbuilding);
        $flat['newbuildingComplex'] = ArrayHelper::toArray($application->flat->newbuildingComplex);

        if (\Yii::$app->request->isPost  && \Yii::$app->request->post('operation')) {
            
            $applicationForm = new ApplicationForm();
            $applicationHistoryForm = new ApplicationHistoryForm();
            $notificationForm = new NotificationForm();
            
            switch(\Yii::$app->request->post('operation')) {
                /**
                 * Admin confirms he has recieved application
                 * and sends request for confirmation to developer
                 */
                case 'approve_app_by_admin':

                    $transaction = \Yii::$app->db->beginTransaction();

                    try {
                        $application->status = 2;
                        $application->save();
        
                        $applicationHistoryForm->application_id = $application->id;
                        $applicationHistoryForm->user_id = \Yii::$app->user->id;
                        $applicationHistoryForm->action = Application::STATUS_RESERV_AWAIT_FOR_APPROVAL;
                        $applicationHistoryModel = (new ApplicationHistory())->fill($applicationHistoryForm->attributes);
                        $applicationHistoryModel->save();
        
                        $notificationForm->initiator_id = \Yii::$app->user->id;
                        $notificationForm->type = 1;
                        $notificationForm->recipient_id = 1111;
                        $notificationForm->topic = 'Требуется подтверждение бронирования по заявке '.$application->application_number;
                        $notificationForm->body = 'Для подтверждения перейдите на страницу заявки';
                        $notificationForm->action_text = 'Перейти';
                        $notificationForm->action_url = '/user/application/view?id='.$application->id;
                        $notificationModel = (new Notification())->fill($notificationForm->attributes);
                        echo '<pre>'; var_dump ($notificationModel); echo '</pre>';die();
            
                        $notificationModel->save();
        
                        $transaction->commit();
                    } catch (\Exception $e) {
                        $transaction->rollBack();
                        echo 'hren vam'; die();
                        return $this->redirectBackWhenException($e);
                    }
                    echo 'Norm!'; die();
                    return $this->redirectWithSuccess(['user/application/view', 'id' => $application->id], 'Успешно. Заявка отправлена застройщику для подтверждения брони.');

                    break;
            }
        }
        
        return $this->inertia('User/Application/View', [
            'application' => ArrayHelper::toArray($application),
            'statusMap' => Application::$status,
            'flat' => $flat,
        ]);
    }
}