<?php

namespace app\controllers\user;

use app\components\traits\CustomRedirects;
use app\components\SharedDataFilter;
use app\models\User;
use app\models\AuthAssignment;
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
                    'view' => ['GET', 'POST'],
                    'update' => ['GET', 'POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'update'],
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
            $applications = $model->getApplicationsForDeveloper(\Yii::$app->user->identity->developer_id)->all();
        } elseif (\Yii::$app->user->can('agent') || \Yii::$app->user->can('manager')) {
            $applications = $model->getApplicationsByAuthor(\Yii::$app->user->id)->all();
        }

        /**
         * Prepare applications array for Vue component
         */
        $applications_array = array();
        foreach ($applications as $application) {
            $application_entry = ArrayHelper::toArray($application);
            $application_entry['author'] = ArrayHelper::toArray($application->applicant);
            $application_entry['author']['roleLabel'] = $application->applicant->roleLabel;
            if(!empty($application->applicant->agency_id)) {
                $application_entry['author']['agency_name'] = $application->applicant->agency->name;
            }
            array_push($applications_array, $application_entry);
        }
               
        return $this->inertia('User/Application/Index', [
            'user' => \Yii::$app->user->identity,
            'applications' => $applications_array,
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
                 * If success application status changes from 1 to 2
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

                        $developerRepresentative = (new User())->getDeveloperRepresentative($application->developer_id);
                        
                        if(!empty($developerRepresentative)) {
                            $notificationForm->initiator_id = \Yii::$app->user->id;
                            $notificationForm->type = 1;
                            $notificationForm->recipient_id = $developerRepresentative->id;
                            $notificationForm->recipient_group = 'developer_repres';
                            $notificationForm->topic = 'Требуется подтверждение бронирования по заявке '.$application->application_number;
                            $notificationForm->body = 'Для подтверждения перейдите на страницу заявки';
                            $notificationForm->action_text = 'Перейти';
                            $notificationForm->action_url = '/user/application/view?id='.$application->id;
                            $notificationModel = (new Notification())->fill($notificationForm->attributes);
                            $notificationModel->save();
                        }
        
                        $transaction->commit();

                        /** Send email-notifications to developer */
                        /*
                        if(!empty($developerRepresentative)) {
                            \Yii::$app->mailer->compose()
                            ->setTo($developerRepresentative->email)
                            ->setFrom([\Yii::$app->params['senderEmail'] => \Yii::$app->params['senderName']])
                            ->setSubject('Поступила новая заявка на бронирование. Требуется подтверждение')
                            ->setTextBody("Ссылка для просмотра заявки: https://grch.ru/user/application/view?id=$application->id")
                            ->send(); 
                        }
                        */

                    } catch (\Exception $e) {
                        $transaction->rollBack();
                        return $this->redirectBackWhenException($e);
                    }
                    return $this->redirect(['view', 'id' => $application->id]);

                    break;

                /** 
                 * developer's reprezentative approves reservation
                 * If success application's status changes from 2 to 3
                 */ 
                case 'approve_reservation_by_developer':

                    $transaction = \Yii::$app->db->beginTransaction();

                    try {
                        $applicationForm->load(\Yii::$app->request->post(), '');
                        //$application->fill($applicationForm->attributes);
                        $application->manager_firstname =  $applicationForm['manager_firstname'];
                        $application->manager_lastname =  $applicationForm['manager_lastname'];
                        $application->manager_middlename =  $applicationForm['manager_middlename'];
                        $application->manager_phone =  $applicationForm['manager_phone'];
                        $application->manager_email =  $applicationForm['manager_email'];
                        $application->reservation_conditions =  $applicationForm['reservation_conditions'];

                        $application->status = 3;
                        $application->save();

                        $applicationHistoryForm->application_id = $application->id;
                        $applicationHistoryForm->user_id = \Yii::$app->user->id;
                        $applicationHistoryForm->action = Application::STATUS_RESERV_APPROVED_BY_DEVELOPER;
                        $applicationHistoryModel = (new ApplicationHistory())->fill($applicationHistoryForm->attributes);
                        $applicationHistoryModel->save();

                        $notificationForm->initiator_id = \Yii::$app->user->id;;
                        $notificationForm->type = 2;
                        $notificationForm->recipient_group = 'admin';
                        $notificationForm->topic = 'Застройщик подтвердил бронирование по заявка '.$application->application_number;
                        $notificationForm->body = 'Для просмотра подробностей и подтверждения перейдите на страницу заявки';
                        $notificationForm->action_text = 'Перейти';
                        $notificationForm->action_url = '/user/application/view?id='.$application->id;
                        $notificationModel = (new Notification())->fill($notificationForm->attributes);              
                        $notificationModel->save();
        
                        $transaction->commit();

                        /** Send email-notifications for admins */
                        /*$admins = (new AuthAssignment())->admins;

                        foreach ($admins as $admin) {
                            if (!empty ($admin)) {
                                \Yii::$app->mailer->compose()
                                ->setTo($admin->email)
                                ->setFrom([\Yii::$app->params['senderEmail'] => \Yii::$app->params['senderName']])
                                ->setSubject("Подтверждение бронирования по заявке $application->application_number от застройщика")
                                ->setTextBody("Для просмотра подробностей и подтверждения перейдите на страницу заявки: https://grch.ru/user/application/view?id=$application->id")
                                ->send(); 
                            }
                        }*/

                    } catch (\Exception $e) {
                        $transaction->rollBack();
                        return $this->redirectBackWhenException($e);
                    }

                    break;
                
                /** 
                 * Admin conrirms reservation (that has been previosly approved by developer's representative) 
                 * If success application's status changes from 
                 * 2 (if developer approved application via email or phone) 
                 * or 3 (if developer approved application via Cabinet)
                 * to 4
                 */ 
                case 'approve_reservation_from_developer_by_admin':

                    $transaction = \Yii::$app->db->beginTransaction();

                    try {
                        $flat = (new Flat())->findOne($application->flat_id);
                        $flat->status = 1;
                        $flat->is_reserved = 1;
                        $flat->save();
                        
                        if ($application->status == 2) {
                            $applicationForm->load(\Yii::$app->request->post(), '');
                            //$application->fill($applicationForm->attributes);
                            $application->manager_firstname =  $applicationForm['manager_firstname'];
                            $application->manager_lastname =  $applicationForm['manager_lastname'];
                            $application->manager_middlename =  $applicationForm['manager_middlename'];
                            $application->manager_phone =  $applicationForm['manager_phone'];
                            $application->manager_email =  $applicationForm['manager_email'];
                            $application->reservation_conditions =  $applicationForm['reservation_conditions'];
                        }
                        
                        $application->status = 4;
                        $application->save();
        
                        $applicationHistoryForm->application_id = $application->id;
                        $applicationHistoryForm->user_id = \Yii::$app->user->id;
                        $applicationHistoryForm->action = Application::STATUS_RESERV_APPROVED_BY_ADMIN;
                        $applicationHistoryModel = (new ApplicationHistory())->fill($applicationHistoryForm->attributes);
                        $applicationHistoryModel->save();

                        $developerRepresentative = (new User())->getDeveloperRepresentative($application->developer_id);
        
                        $notificationForm->initiator_id = \Yii::$app->user->id;
                        $notificationForm->type = 1;
                        $notificationForm->recipient_id = $application->applicant_id;
                        $notificationForm->topic = 'Одобрение брони по заявке '.$application->application_number;
                        $notificationForm->body = 'Перейдите на страницу заявки, чтобы подтвердить получение одобрения брони и взять заявку в работу';
                        $notificationForm->action_text = 'Перейти';
                        $notificationForm->action_url = '/user/application/view?id='.$application->id;
                        $notificationModel = (new Notification())->fill($notificationForm->attributes);
            
                        $notificationModel->save();
        
                        $transaction->commit();

                        /** Send email-notifications to agent or admin */
                        /*\Yii::$app->mailer->compose()
                        ->setTo($application->applicant->email)
                        ->setFrom([\Yii::$app->params['senderEmail'] => \Yii::$app->params['senderName']])
                        ->setSubject('Бронь одобрена. Пожалуйста, подтвердите.')
                        ->setTextBody("Броно по заявке $application->application_number одобрена. Подтвердите получение информации и возьмите заявку в работу: https://grch.ru/user/application/view?id=$application->id")
                        ->send();*/

                    } catch (\Exception $e) {
                        $transaction->rollBack();
                        return $this->redirectBackWhenException($e);
                    }
                    return $this->redirect(['view', 'id' => $application->id]);

                    break;

                /**
                 * Agent or manager takes application to work
                 * If success application's status changes from 4 to 5
                 */
                case 'take_in_work_by_agent':
                case 'take_in_work_by_manager':
                    
                    $transaction = \Yii::$app->db->beginTransaction();

                    try {
                        $application->status = 5;
                        $application->save();

                        $applicationHistoryForm->application_id = $application->id;
                        $applicationHistoryForm->user_id = \Yii::$app->user->id;
                        $applicationHistoryForm->action = Application::STATUS_APPLICATION_IN_WORK;
                        $applicationHistoryModel = (new ApplicationHistory())->fill($applicationHistoryForm->attributes);
                        $applicationHistoryModel->save();

                        $notificationForm->initiator_id = \Yii::$app->user->id;;
                        $notificationForm->type = 2;
                        $notificationForm->recipient_group = 'admin';
                        $notificationForm->topic = 'Агент подтвердил получение одобрения брони по заявке '.$application->application_number.'. Заявка в работе.';
                        $notificationForm->body = 'Для просмотра подробностей перейдите на страницу заявки';
                        $notificationForm->action_text = 'Перейти';
                        $notificationForm->action_url = '/user/application/view?id='.$application->id;
                        $notificationModel = (new Notification())->fill($notificationForm->attributes);              
                        $notificationModel->save();
        
                        $transaction->commit();

                        /** Send email-notifications for admins */
                        /*$admins = (new AuthAssignment())->admins;

                        foreach ($admins as $admin) {
                            if (!empty ($admin)) {
                                \Yii::$app->mailer->compose()
                                ->setTo($admin->email)
                                ->setFrom([\Yii::$app->params['senderEmail'] => \Yii::$app->params['senderName']])
                                ->setSubject("Агент подтвердил получение одобрения брони по заявке $application->application_number")
                                ->setTextBody("Для просмотра подробностей перейдите на страницу заявки: https://grch.ru/user/application/view?id=$application->id")
                                ->send(); 
                            }
                        }*/

                    } catch (\Exception $e) {
                        $transaction->rollBack();
                        return $this->redirectBackWhenException($e);
                    }

                    break;
                
                /**
                 * Agent or maneger reports successful deal
                 * If success, application status changes from 5 to 9
                 */
                case 'report_success_deal_by_agent':
                case 'report_success_deal_by_manager':

                    $transaction = \Yii::$app->db->beginTransaction();

                    try {
                        $application->status = 9;
                        $application->save();

                        $applicationHistoryForm->application_id = $application->id;
                        $applicationHistoryForm->user_id = \Yii::$app->user->id;
                        $applicationHistoryForm->action = Application::STATUS_APPLICATION_APPROVAL_REQUEST;
                        $applicationHistoryModel = (new ApplicationHistory())->fill($applicationHistoryForm->attributes);
                        //echo '<pre>'; var_dump($applicationHistoryModel); echo '</pre>'; die();
                        $applicationHistoryModel->save();

                        $notificationForm->initiator_id = \Yii::$app->user->id;;
                        $notificationForm->type = 2;
                        $notificationForm->recipient_group = 'admin';
                        $notificationForm->topic = 'Агент сообщил о завершении сделки по заявке '.$application->application_number.'. Подтвердите.';
                        $notificationForm->body = 'Для просмотра подробностей и подтверждения перейдите на страницу заявки';
                        $notificationForm->action_text = 'Перейти';
                        $notificationForm->action_url = '/user/application/view?id='.$application->id;
                        $notificationModel = (new Notification())->fill($notificationForm->attributes);              
                        $notificationModel->save();
        
                        $transaction->commit();

                        /** Send email-notifications for admins */
                        /*$admins = (new AuthAssignment())->admins;

                        foreach ($admins as $admin) {
                            if (!empty ($admin)) {
                                \Yii::$app->mailer->compose()
                                ->setTo($admin->email)
                                ->setFrom([\Yii::$app->params['senderEmail'] => \Yii::$app->params['senderName']])
                                ->setSubject("Агент сообщил о завершении сделки по заявке $application->application_number")
                                ->setTextBody("Для просмотра подробностей и подтверждения перейдите на страницу заявки: https://grch.ru/user/application/view?id=$application->id")
                                ->send(); 
                            }
                        }*/

                    } catch (\Exception $e) {
                        $transaction->rollBack();
                        return $this->redirectBackWhenException($e);
                    }                    

                    break;

                /**
                 * Admin confirms recieving information about successful deal (from agent or manager)
                 * If success, application status changes from 9 to 10 
                 */
                case 'confirm_recieving_success_deal_info_by_admin':
                    
                    $transaction = \Yii::$app->db->beginTransaction();

                    try {
                        $application->status = 10;
                        $application->save();

                        $applicationHistoryForm->application_id = $application->id;
                        $applicationHistoryForm->user_id = \Yii::$app->user->id;
                        $applicationHistoryForm->action = Application::STATUS_APPLICATION_APPROVAL_PROCESS;
                        $applicationHistoryModel = (new ApplicationHistory())->fill($applicationHistoryForm->attributes);
                        $applicationHistoryModel->save();

                        $notificationForm->initiator_id = \Yii::$app->user->id;;
                        $notificationForm->type = 1;
                        $notificationForm->recipient_id = $application->applicant_id;
                        $notificationForm->topic = 'Администратор получил информацию о завершении сделки по заявке '.$application->application_number.'. Дождитесь её обработки.';
                        $notificationForm->body = 'Для просмотра подробностей перейдите на страницу заявки';
                        $notificationForm->action_text = 'Перейти';
                        $notificationForm->action_url = '/user/application/view?id='.$application->id;
                        $notificationModel = (new Notification())->fill($notificationForm->attributes);              
                        $notificationModel->save();
        
                        $transaction->commit();

                        /** Send email-notifications to agent or admin */
                        /*\Yii::$app->mailer->compose()
                        ->setTo($application->applicant->email)
                        ->setFrom([\Yii::$app->params['senderEmail'] => \Yii::$app->params['senderName']])
                        ->setSubject("Администратор получил информацию о завершении сделки по заявке $application->application_number. Дождитесь её обработки.")
                        ->setTextBody("Администратор получил информацию о завершении сделки по заявке $application->application_number. Пожалуйста, дождитесь её обработки. Статус заявки Вы можете отслеживать на странице https://grch.ru/user/application/view?id=$application->id")
                        ->send();*/

                    } catch (\Exception $e) {
                        $transaction->rollBack();
                        return $this->redirectBackWhenException($e);
                    }

                    break;

                /**
                 * Admin approves deal success. The eal is finished
                 * If success, application status changes from 10 to 11 
                 */
                case 'confirm_success_deal_by_admin':

                    $transaction = \Yii::$app->db->beginTransaction();

                    try {
                        $flat = (new Flat())->findOne($application->flat_id);
                        $flat->status = 2;
                        $flat->sold_by_application = 1;
                        $flat->is_applicated = 0;
                        $flat->is_reserved = 0;
                        $flat->save();

                        $application->status = 11;
                        $application->save();

                        $applicationHistoryForm->application_id = $application->id;
                        $applicationHistoryForm->user_id = \Yii::$app->user->id;
                        $applicationHistoryForm->action = Application::STATUS_APPLICATION_SUCCESS;
                        $applicationHistoryModel = (new ApplicationHistory())->fill($applicationHistoryForm->attributes);
                        $applicationHistoryModel->save();

                        $notificationForm->initiator_id = \Yii::$app->user->id;;
                        $notificationForm->type = 1;
                        $notificationForm->recipient_id = $application->applicant_id;
                        $notificationForm->topic = 'Информация о завершении сделки по заявке '.$application->application_number.' подтверждена.';
                        $notificationForm->body = 'Администратор подтвердил успешное завершение сделки. Ожидается оплата. Для просмотра дополнительной информации перейдите на страницу заявки';
                        $notificationForm->action_text = 'Перейти';
                        $notificationForm->action_url = '/user/application/view?id='.$application->id;
                        $notificationModel = (new Notification())->fill($notificationForm->attributes);              
                        $notificationModel->save();
        
                        $transaction->commit();

                        /** Send email-notifications to agent or admin */
                        /*\Yii::$app->mailer->compose()
                        ->setTo($application->applicant->email)
                        ->setFrom([\Yii::$app->params['senderEmail'] => \Yii::$app->params['senderName']])
                        ->setSubject("Информация о завершении сделки по заявке $application->application_number подтверждена.")
                        ->setTextBody("Администратор подтвердил успешное завершение сделки по заявке $application->application_number. Ожидается оплата. Для просмотра дополнительной информации перейдите на страницу заявки: https://grch.ru/user/application/view?id=$application->id")
                        ->send();*/

                    } catch (\Exception $e) {
                        $transaction->rollBack();
                        return $this->redirectBackWhenException($e);
                    }

                    break;
            }
        }

        $application_array = ArrayHelper::toArray($application);
        $application_array['author'] = ArrayHelper::toArray($application->applicant);
        $application_array['author']['roleLabel'] = $application->applicant->roleLabel;
        if(!empty($application->applicant->agency_id)) {
            $application_array['author']['agency_name'] = $application->applicant->agency->name;
        }
        
        return $this->inertia('User/Application/View', [
            // 'application' => ArrayHelper::toArray($application),
            'application' => $application_array,
            'applicationHistory' => ArrayHelper::toArray($application->history),
            'statusMap' => Application::$status,
            'flat' => $flat,
        ]);
    }

    public function actionUpdate($id)
    {
        $application = (new Application())->findOne($id);

        return $this->inertia('User/Application/Update', [
            'application' => ArrayHelper::toArray($application),
            'statusMap' => Application::$status,
        ]);   
    }
}