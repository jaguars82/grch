<?php

namespace app\controllers\user;

use app\components\traits\CustomRedirects;
use app\components\SharedDataFilter;
use app\models\User;
use app\models\AuthAssignment;
use app\models\Developer;
use app\models\NewbuildingComplex;
use app\models\Newbuilding;
use app\models\Entrance;
use app\models\Flat;
use app\models\Agency;
use app\models\Application;
use app\models\ApplicationDocument;
use app\models\form\ApplicationForm;
use app\models\ApplicationHistory;
use app\models\form\ApplicationHistoryForm;
use app\models\Notification;
use app\models\form\NotificationForm;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use tebe\inertia\web\Controller;
use yii\helpers\ArrayHelper;
use yii\data\Pagination;

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
        if(\Yii::$app->request->isPost) {

            switch(\Yii::$app->request->post('operation')) {
                case 'moveToArchive':
                    $applicatonToArchive = (new Application())->findOne(\Yii::$app->request->post('id'));
                    $applicatonToArchive->is_active = 0;
                    $applicatonToArchive->save();
                    break;
            }
        }

        $query = Application::find()->where(['is_active' => 1]);

        /** Filter by date (creation or update) */
        if (is_array(\Yii::$app->request->get('dateRange'))) {
            $dateRange = \Yii::$app->request->get('dateRange');
            $dateFrom = str_ireplace("/", "-", $dateRange['from']);
            $dateTo = str_ireplace("/", "-", $dateRange['to']);
            $dateField = !empty(\Yii::$app->request->get('dateParam')) ? \Yii::$app->request->get('dateParam') : 'created_at';
            $query->andWhere(['>=', 'application.'.$dateField, $dateFrom.'  00:00:00'])->andWhere(['<=', 'application.'.$dateField, $dateTo.'  23:59:59']);
        }

        /** Filter by agency */
        if (null !== \Yii::$app->request->get('agency')) {
            $query->join('LEFT JOIN', 'user', 'user.id = applicant_id')
                ->andWhere(['user.agency_id' => \Yii::$app->request->get('agency')]);
        }
        
        /** Filter by agent */
        if (null !== \Yii::$app->request->get('agent')) {
            $query->andWhere(['applicant_id' => \Yii::$app->request->get('agent')]);
        }
        
        if (\Yii::$app->user->identity->role === 'developer_repres') {
            $query->andWhere(['developer_id' => \Yii::$app->user->identity->developer_id]);
        } 

        if (\Yii::$app->user->identity->role === 'agent') {
            $query->andWhere(['applicant_id' => \Yii::$app->user->id]);
        }

        if (\Yii::$app->user->identity->role === 'manager') {
            if (
                null !== \Yii::$app->request->post('show') && \Yii::$app->request->post('show') === 'agency'
                || null !== \Yii::$app->request->get('show') && \Yii::$app->request->get('show') === 'agency'
            ) {
                $show = \Yii::$app->request->isPost ? \Yii::$app->request->post('show') : \Yii::$app->request->get('show');
                $query->join('LEFT JOIN', 'user', 'user.id = applicant_id')
                ->andWhere(['user.agency_id' => \Yii::$app->user->identity->agency_id]);
            } else {
               $query->andWhere(['applicant_id' => \Yii::$app->user->id]);
            }
        }
            
        // get the total number of applications
        $count = $query->count();

        // create a pagination object with the total count
        $pagination = new Pagination(['totalCount' => $count]);

        if (!empty(\Yii::$app->request->get('psize'))) {
            $pagination->setPageSize(\Yii::$app->request->get('psize'));
        }

        // limit the query using the pagination and retrieve the applications
        $applications = $query
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy(['updated_at' => SORT_DESC])
            ->all();

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

        /** Opttions for agents filter */
        if (\Yii::$app->user->identity->role === 'admin' && null !== \Yii::$app->request->get('agency')) {
            $agents = Agency::getUsersByAgency(\Yii::$app->request->get('agency'));
        }
        if (\Yii::$app->user->identity->role === 'manager') {
            $agents = Agency::getUsersByAgency(\Yii::$app->user->identity->agency_id);
        }
               
        return $this->inertia('User/Application/Index', [
            'user' => \Yii::$app->user->identity,
            'applications' => $applications_array,
            'statusMap' => Application::$status,
            'totalRows' => $count,
            'page' => $pagination->page,
            'psize' => $pagination->pageSize,
            'show' => isset($show) ? $show : 'self',
            'agencies' => \Yii::$app->user->identity->role === 'admin' ? Agency::getAllAsList() : [],
            'agents' => isset($agents) ? ArrayHelper::toArray($agents) : [],
        ]);
    }

    public function actionView($id)
    {
        $application = (new Application())->findOne($id);
        $flat = ArrayHelper::toArray($application->flat);
        $flat['developer'] = ArrayHelper::toArray($application->flat->developer);
        $flat['entrance'] = ArrayHelper::toArray($application->flat->entrance);
        $flat['newbuilding'] = ArrayHelper::toArray($application->flat->newbuilding);
        $flat['newbuildingComplex'] = ArrayHelper::toArray($application->flat->newbuildingComplex, [
            'app\models\NewbuildingComplex' => [
                'id', 'name', 'logo',
                'address' => function ($nbc) {
                    return $nbc->address;
                },
            ]
        ]);

        if (\Yii::$app->request->isPost  && \Yii::$app->request->post('operation')) {
            
            $applicationForm = new ApplicationForm();
            $applicationHistoryForm = new ApplicationHistoryForm();
            $notificationForm = new NotificationForm();
            
            switch(\Yii::$app->request->post('operation')) {
                /**
                 * Change object (flat)
                 */
                case 'change_object':
                    try {
                        $application->flat_id = \Yii::$app->request->post('new_object_id');
                        $application->save();
                    } catch (\Exception $e) {
                        $transaction->rollBack();
                        return $this->redirectBackWhenException($e);
                    }
                    return $this->redirect(['view', 'id' => $application->id]);
                    break;
                /**
                 * Admin confirms he has recieved application
                 * and sends request for confirmation to developer
                 * If success application status changes from 1 to 2
                 */
                case 'approve_app_by_admin':

                    $transaction = \Yii::$app->db->beginTransaction();

                    try {
                        $application->status = Application::STATUS_RESERV_AWAIT_FOR_APPROVAL;
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
                        if(!empty($developerRepresentative)) {
                            \Yii::$app->mailer->compose()
                            ->setTo($developerRepresentative->email)
                            ->setFrom([\Yii::$app->params['senderEmail'] => \Yii::$app->params['senderName']])
                            ->setSubject('Поступила новая заявка на бронирование. Требуется подтверждение')
                            ->setTextBody("Ссылка для просмотра заявки: https://grch.ru/user/application/view?id=$application->id")
                            ->send(); 
                        }

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
                        $application->is_toll =  $applicationForm['is_toll'];

                        $application->status = Application::STATUS_RESERV_APPROVED_BY_DEVELOPER;
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
                        $admins = (new AuthAssignment())->admins;

                        foreach ($admins as $admin) {
                            if (!empty ($admin)) {
                                \Yii::$app->mailer->compose()
                                ->setTo($admin->email)
                                ->setFrom([\Yii::$app->params['senderEmail'] => \Yii::$app->params['senderName']])
                                ->setSubject("Подтверждение бронирования по заявке $application->application_number от застройщика")
                                ->setTextBody("Представитель застройщика подтвердил бронь через свой личный кабинет. Для просмотра подробностей и подтверждения перейдите на страницу заявки: https://grch.ru/user/application/view?id=$application->id")
                                ->send(); 
                            }
                        }

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
                        $flat = Flat::findOne($application->flat_id);
                        $flat->status = Application::STATUS_RESERV_APPROVED_BY_ADMIN;
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
                            $application->is_toll =  $applicationForm['is_toll'];
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

                        /** Send email-notifications to agent or manager */
                        \Yii::$app->mailer->compose()
                        ->setTo($application->applicant->email)
                        ->setFrom([\Yii::$app->params['senderEmail'] => \Yii::$app->params['senderName']])
                        ->setSubject('Бронь одобрена. Пожалуйста, подтвердите.')
                        ->setTextBody("Бронь по заявке $application->application_number одобрена. Подтвердите получение информации и возьмите заявку в работу: https://grch.ru/user/application/view?id=$application->id")
                        ->send();

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
                        $applicationForm->load(\Yii::$app->request->post(), '');

                        if ($application->is_toll === 1) {
                            $applicationForm->processRecieptFile();

                            if (count($applicationForm->recieptFile)) {
                                
                                $application->receipt_provided = 1;
                                
                                foreach ($applicationForm->recieptFile as $ind => $file) {
                                    $reciept = new ApplicationDocument();
                                    $reciept->application_id = $application->id;
                                    $reciept->user_id = \Yii::$app->user->id;
                                    $reciept->category = ApplicationDocument::CAT_RECIEPT;
                                    $reciept->file = $applicationForm->recieptFilesToSave[$ind];
                                    $reciept->name = $file['name'];
                                    $reciept->size = $file['size'];
                                    $reciept->filetype = $file['extension'];
                                    $reciept->save();
                                }
                            }
                        }

                        $applicationForm->processAgentDocPackFile();

                        if (count($applicationForm->agentDocpack)) {
                                
                            $application->agent_docpack_provided = 1;
                            
                            foreach ($applicationForm->agentDocpack as $ind => $file) {
                                $doc = new ApplicationDocument();
                                $doc->application_id = $application->id;
                                $doc->user_id = \Yii::$app->user->id;
                                $doc->category = ApplicationDocument::AGENT_DOCPACK;
                                $doc->file = $applicationForm->agentDocpackToSave[$ind];
                                $doc->name = $file['name'];
                                $doc->size = $file['size'];
                                $doc->filetype = $file['extension'];
                                $doc->save();
                            }
                        }

                        $appStatus = $application->agent_docpack_provided === 1 ? Application::STATUS_APPLICATION_IN_WORK_AGENT_DOCPACK_PROVIDED : Application::STATUS_APPLICATION_IN_WORK;

                        $application->status = $appStatus;
                        $application->save();

                        $applicationHistoryForm->application_id = $application->id;
                        $applicationHistoryForm->user_id = \Yii::$app->user->id;
                        $applicationHistoryForm->action = $appStatus;
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

                        /** Notification for developer (if agent's documents pack uploaded) */
                        if ($application->agent_docpack_provided === 1) {

                            $developerRepresentative = (new User())->getDeveloperRepresentative($application->developer_id);
                        
                            if(!empty($developerRepresentative)) {
                                $notificationForm->type = 1;
                                $notificationForm->recipient_id = $developerRepresentative->id;
                                $notificationForm->recipient_group = 'developer_repres';
                                $notificationForm->topic = 'Агент загрузил документы по заявке '.$application->application_number;
                                $notificationForm->body = 'Для просмотра документов перейдите на страницу заявки';
                                $notificationForm->action_text = 'Перейти';
                                $notificationForm->action_url = '/user/application/view?id='.$application->id;
                                $notificationForDeveloper = (new Notification())->fill($notificationForm->attributes);
                                $notificationForDeveloper->save();
                            }

                        }
        
                        $transaction->commit();

                        /** Send email-notifications for admins */
                        $admins = (new AuthAssignment())->admins;

                        foreach ($admins as $admin) {
                            if (!empty ($admin)) {
                                \Yii::$app->mailer->compose()
                                ->setTo($admin->email)
                                ->setFrom([\Yii::$app->params['senderEmail'] => \Yii::$app->params['senderName']])
                                ->setSubject("Агент подтвердил получение одобрения брони по заявке $application->application_number")
                                ->setTextBody("Для просмотра подробностей перейдите на страницу заявки: https://grch.ru/user/application/view?id=$application->id")
                                ->send(); 
                            }
                        }

                        /** Send email-notifications to developer */
                        if ($application->agent_docpack_provided === 1) {
                            if(!empty($developerRepresentative)) {
                                \Yii::$app->mailer->compose()
                                ->setTo($developerRepresentative->email)
                                ->setFrom([\Yii::$app->params['senderEmail'] => \Yii::$app->params['senderName']])
                                ->setSubject("Агент загрузил документы по заявке $application->application_number")
                                ->setTextBody("Для просмотра документов перейдите на страницу заявки: https://grch.ru/user/application/view?id=$application->id")
                                ->send(); 
                            }
                        }

                    } catch (\Exception $e) {
                        $transaction->rollBack();
                        return $this->redirectBackWhenException($e);
                    }

                    break;

                /**
                 * Developer uploads documents pack for agent
                 */
                case 'upload_developer_docpack':
                    
                    $transaction = \Yii::$app->db->beginTransaction();

                    try {
                        $applicationForm->load(\Yii::$app->request->post(), '');

                        $applicationForm->processdeveloperDocPackFile();

                        if (count($applicationForm->developerDocpack)) {

                            $application->developer_docpack_provided = 1;
                            
                            foreach ($applicationForm->developerDocpack as $ind => $file) {
                                $doc = new ApplicationDocument();
                                $doc->application_id = $application->id;
                                $doc->user_id = \Yii::$app->user->id;
                                $doc->category = ApplicationDocument::DEVELOPER_DOCPACK;
                                $doc->file = $applicationForm->developerDocpackToSave[$ind];
                                $doc->name = $file['name'];
                                $doc->size = $file['size'];
                                $doc->filetype = $file['extension'];
                                $doc->save();
                            }
                        }

                        if ($application->developer_docpack_provided === 1) {
                            $application->status = Application::STATUS_APPLICATION_IN_WORK_DEVELOPER_DOCPACK_PROVIDED;
                            $application->save();
    
                            $this->storeToHistory($applicationHistoryForm, $application, Application::STATUS_APPLICATION_IN_WORK_DEVELOPER_DOCPACK_PROVIDED);
    
                            $this->sendNotification(
                                $notificationForm, Notification::DEVELOPER_TO_USER,
                                'Застройщик загрузил документы по заявке '.$application->application_number,
                                'Для просмотра документов перейдите на страницу заявки',
                                'Перейти',
                                '/user/application/view?id='.$application->id,
                                $application->applicant_id
                            );
    
                            $transaction->commit();

                            /** Send email-notifications to agent or manager */
                            \Yii::$app->mailer->compose()
                            ->setTo($application->applicant->email)
                            ->setFrom([\Yii::$app->params['senderEmail'] => \Yii::$app->params['senderName']])
                            ->setSubject('Застройщик загрузил документы по заявке '.$application->application_number)
                            ->setTextBody("Для просмотра документов перейдите на страницу заявки: https://grch.ru/user/application/view?id=$application->id")
                            ->send();
                        }

                    } catch (\Exception $e) {
                        $transaction->rollBack();
                        return $this->redirectBackWhenException($e);
                    }

                    break;

                /**
                 * Agent or manager uploads DDU
                 */
                case 'upload_ddu_by_agent':
                case 'upload_ddu_by_manager':

                    $transaction = \Yii::$app->db->beginTransaction();

                    try {
                        $applicationForm->load(\Yii::$app->request->post(), '');

                        $applicationForm->processDduFile();

                        if (count($applicationForm->dduFile)) {

                            $application->ddu_provided = 1;
                            
                            foreach ($applicationForm->dduFile as $ind => $file) {
                                $ddu = new ApplicationDocument();
                                $ddu->application_id = $application->id;
                                $ddu->user_id = \Yii::$app->user->id;
                                $ddu->category = ApplicationDocument::CAT_DDU;
                                $ddu->file = $applicationForm->dduFilesToSave[$ind];
                                $ddu->name = $file['name'];
                                $ddu->size = $file['size'];
                                $ddu->filetype = $file['extension'];
                                $ddu->save();
                            }
                        }

                        $application->ddu_price = $applicationForm->ddu_price;
                        $application->ddu_cash = $applicationForm->ddu_cash;
                        $application->ddu_mortgage = $applicationForm->ddu_mortgage;
                        $application->ddu_matcap = $applicationForm->ddu_matcap;
                        $application->ddu_cash_paydate = $applicationForm->ddu_cash_paydate;
                        $application->ddu_mortgage_paydate = $applicationForm->ddu_mortgage_paydate;
                        $application->ddu_matcap_paydate = $applicationForm->ddu_matcap_paydate;
                        $application->status = Application::STATUS_DDU_UPLOADED;
                        $application->save();

                        $applicationHistoryForm->application_id = $application->id;
                        $applicationHistoryForm->user_id = \Yii::$app->user->id;
                        $applicationHistoryForm->action = Application::STATUS_DDU_UPLOADED;
                        $applicationHistoryModel = (new ApplicationHistory())->fill($applicationHistoryForm->attributes);
                        $applicationHistoryModel->save();

                        $notificationForm->initiator_id = \Yii::$app->user->id;;
                        $notificationForm->type = 2;
                        $notificationForm->recipient_group = 'admin';
                        $notificationForm->topic = 'Агент загрузил Договор долевого участия по заявке '.$application->application_number.'.';
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
                                ->setSubject("Агент загрузил Договор долевого участия по заявке $application->application_number")
                                ->setTextBody("Для просмотра подробностей перейдите на страницу заявки: https://grch.ru/user/application/view?id=$application->id")
                                ->send(); 
                            }
                        }*/

                    } catch (\Exception $e) {
                        $transaction->rollBack();
                        return $this->redirectBackWhenException($e);
                    }

                    break;

                case 'issue_invoice_to_developer':

                    $transaction = \Yii::$app->db->beginTransaction();

                    try {
                        $application->status = Application::STATUS_INVOICE_TO_DEVELOPER_ISSUED;
                        $application->save();
        
                        $applicationHistoryForm->application_id = $application->id;
                        $applicationHistoryForm->user_id = \Yii::$app->user->id;
                        $applicationHistoryForm->action = Application::STATUS_INVOICE_TO_DEVELOPER_ISSUED;
                        $applicationHistoryModel = (new ApplicationHistory())->fill($applicationHistoryForm->attributes);
                        $applicationHistoryModel->save();

                        $developerRepresentative = (new User())->getDeveloperRepresentative($application->developer_id);
                        
                        if(!empty($developerRepresentative)) {
                            $notificationForm->initiator_id = \Yii::$app->user->id;
                            $notificationForm->type = 1;
                            $notificationForm->recipient_id = $developerRepresentative->id;
                            $notificationForm->recipient_group = 'developer_repres';
                            $notificationForm->topic = 'Выставлен счет на оплату вознаграждения по заявке '.$application->application_number;
                            $notificationForm->body = 'Подтвердить оплату счёта Вы можете на странице заявки';
                            $notificationForm->action_text = 'Перейти';
                            $notificationForm->action_url = '/user/application/view?id='.$application->id;
                            $notificationModel = (new Notification())->fill($notificationForm->attributes);
                            $notificationModel->save();
                        }
        
                        $transaction->commit();

                        /** Send email-notifications to developer */
                        if(!empty($developerRepresentative)) {
                            \Yii::$app->mailer->compose()
                            ->setTo($developerRepresentative->email)
                            ->setFrom([\Yii::$app->params['senderEmail'] => \Yii::$app->params['senderName']])
                            ->setSubject('Выставлен счет на оплату вознаграждения по заявке '.$application->application_number)
                            ->setTextBody("Потдвердить оплату счёта Вы можете на странице заявки. Ссылка для просмотра заявки: https://grch.ru/user/application/view?id=$application->id")
                            ->send(); 
                        }

                    } catch (\Exception $e) {
                        $transaction->rollBack();
                        return $this->redirectBackWhenException($e);
                    }
                    return $this->redirect(['view', 'id' => $application->id]);

                    break;

                /**
                 * Developer reports reward payment
                 */
                case 'report_payment_from_developer':

                    $transaction = \Yii::$app->db->beginTransaction();

                    try {
                        $application->status = Application::STATUS_COMISSION_PAY_CONFIRMED_BY_DEVELOPER;
                        $application->save();

                        $this->storeToHistory($applicationHistoryForm, $application, Application::STATUS_COMISSION_PAY_CONFIRMED_BY_DEVELOPER);

                        $this->sendNotification(
                            $notificationForm, Notification::DEVELOPER_TO_ADMINS,
                            'Застройщик подтвердил выплату вознаграждения по заявке '.$application->application_number,
                            'Для просмотра подробностей и подтверждения перейдите на страницу заявки',
                            'Перейти',
                            '/user/application/view?id='.$application->id,
                        );

                        $transaction->commit();

                        /** Send email-notifications for admins */
                        $admins = (new AuthAssignment())->admins;

                        foreach ($admins as $admin) {
                            if (!empty ($admin)) {
                                \Yii::$app->mailer->compose()
                                ->setTo($admin->email)
                                ->setFrom([\Yii::$app->params['senderEmail'] => \Yii::$app->params['senderName']])
                                ->setSubject("Застройщик подтвердил выплату вознаграждения по заявке $application->application_number от застройщика")
                                ->setTextBody("Представитель застройщика подтвердил выплату вознаграждения через свой личный кабинет. Для просмотра подробностей и подтверждения перейдите на страницу заявки: https://grch.ru/user/application/view?id=$application->id")
                                ->send(); 
                            }
                        }

                    } catch (\Exception $e) {
                        $transaction->rollBack();
                        return $this->redirectBackWhenException($e);
                    }
                    return $this->redirect(['view', 'id' => $application->id]);

                    break;

                /**
                 * Admin confirms recieving the reward payment from developer
                 */
                case 'confirm_payment_from_developer_by_admin':

                    $transaction = \Yii::$app->db->beginTransaction();

                    try {
                        $application->status = Application::STATUS_COMISSION_PAY_CONFIRMED_BY_ADMIN;
                        $application->save();

                        $this->storeToHistory($applicationHistoryForm, $application, Application::STATUS_COMISSION_PAY_CONFIRMED_BY_ADMIN);

                        $this->sendNotification(
                            $notificationForm, Notification::ADMIN_TO_USER,
                            'Администратор подтвердил получение вознаграждения от застройщика по заявке '.$application->application_number,
                            'Перейдите на страницу заявки, чтобы скачать Отчет-Акт',
                            'Перейти',
                            '/user/application/view?id='.$application->id,
                            $application->applicant_id
                        );

                        $transaction->commit();

                        /** Send email-notification for agent or manager */
                        \Yii::$app->mailer->compose()
                        ->setTo($application->applicant->email)
                        ->setFrom([\Yii::$app->params['senderEmail'] => \Yii::$app->params['senderName']])
                        ->setSubject('Вознаграждение от застройщика получено. Пожалуйста, заполните Отчет-Акт')
                        ->setTextBody("Администратор подтвердил получение вознаграждения от застройщика по заявке $application->application_number. Заполните и загрузите Отчет-Акт: https://grch.ru/user/application/view?id=$application->id")
                        ->send();

                    } catch (\Exception $e) {
                        $transaction->rollBack();
                        return $this->redirectBackWhenException($e);
                    }
                    return $this->redirect(['view', 'id' => $application->id]);

                    break;

                /***
                 * Agent or manager reports successful deal
                 * uploads Report-Act
                 */
                case 'issue_report_act':

                    $transaction = \Yii::$app->db->beginTransaction();

                    try {
                        $application->status = Application::STATUS_APPLICATION_APPROVAL_REQUEST;

                        $applicationForm->processReportActFile();

                        if (count($applicationForm->reportActFile)) {

                            $application->report_act_provided = 1;
                            
                            foreach ($applicationForm->reportActFile as $ind => $file) {
                                $reportAct = new ApplicationDocument();
                                $reportAct->application_id = $application->id;
                                $reportAct->user_id = \Yii::$app->user->id;
                                $reportAct->category = ApplicationDocument::CAT_REPORT_ACT;
                                $reportAct->file = $applicationForm->reportActToSave[$ind];
                                $reportAct->name = $file['name'];
                                $reportAct->size = $file['size'];
                                $reportAct->filetype = $file['extension'];
                                $reportAct->save();
                            }
                        }

                        $application->save();

                        $this->storeToHistory($applicationHistoryForm, $application, Application::STATUS_APPLICATION_APPROVAL_REQUEST);

                        $this->sendNotification(
                            $notificationForm, Notification::USER_TO_ADMINS,
                            'Агент загрузил Отчет-Акт по заявке '.$application->application_number,
                            'Пожалуйста, проверьте информацию',
                            'Перейти',
                            '/user/application/view?id='.$application->id,
                            $application->applicant_id
                        );

                        $transaction->commit();

                        /**
                         * Send email message to admins
                         */
                        /*foreach ($admins as $admin) {
                            if (!empty ($admin)) {
                                \Yii::$app->mailer->compose()
                                ->setTo($application->admin->email)
                                ->setFrom([\Yii::$app->params['senderEmail'] => \Yii::$app->params['senderName']])
                                ->setSubject('Вознаграждение от застройщика получено. Пожалуйста, заполните Отчет-Акт')
                                ->setTextBody("Агент загрузил Отчет-Акт по заявке $application->application_number. Пожалуйста, проверьте информацию: https://grch.ru/user/application/view?id=$application->id")
                                ->send();
                            }
                        }*/

                    } catch (\Exception $e) {
                        $transaction->rollBack();
                        return $this->redirectBackWhenException($e);
                    }
                    return $this->redirect(['view', 'id' => $application->id]);

                break;
                
                /**
                 * Agent or manager reports successful deal
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
                        $flat = Flat::findOne($application->flat_id);
                        $flat->status = 2;
                        $flat->sold_by_application = 1;
                        $flat->is_applicated = Flat::APPLICATED_NONE;
                        $flat->is_reserved = 0;
                        $flat->save();

                        $application->status = Application::STATUS_APPLICATION_SUCCESS;
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
                        $notificationForm->body = 'Администратор подтвердил успешное завершение сделки.';
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
                        ->setTextBody("Администратор подтвердил успешное завершение сделки по заявке $application->application_number. Для просмотра дополнительной информации перейдите на страницу заявки: https://grch.ru/user/application/view?id=$application->id")
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
        $application_array['documents'] = [
            'amount' => count($application->documents),
            'reciepts' => ArrayHelper::toArray($application->reciepts),
            'agentDocpack' => ArrayHelper::toArray($application->agentDocpack),
            'developerDocpack' => ArrayHelper::toArray($application->developerDocpack),
            'ddus' => ArrayHelper::toArray($application->ddus),
            'reportAct' => ArrayHelper::toArray($application->reportAct),
        ];
        
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
        $application = Application::findOne($id);
        $applicationArr = ArrayHelper::toArray($application);
        $applicationArr['flat'] = ArrayHelper::toArray($application->flat);
        $applicationArr['flat']['entrance'] = ArrayHelper::toArray($application->flat->entrance);
        $applicationArr['flat']['newbuilding'] = ArrayHelper::toArray($application->flat->newbuilding);
        $applicationArr['flat']['newbuildingComplex'] = ArrayHelper::toArray($application->flat->newbuilding->newbuildingComplex);
        $applicationArr['flat']['developer'] = ArrayHelper::toArray($application->flat->newbuilding->newbuildingComplex->developer);

        return $this->inertia('User/Application/Update', [
            'application' => $applicationArr,
            'statusMap' => Application::$status,
            'eOperation' => \Yii::$app->request->post('eOperation') ? \Yii::$app->request->post('eOperation') : '',
            'developers' => \Yii::$app->request->post('eOperation') === 'change_object' ? Developer::getAllAsList() : [],
            'buildingComplexes' => \Yii::$app->request->post('developerId') ? NewbuildingComplex::find()->where(['developer_id' => \Yii::$app->request->post('developerId')])->select(['id', 'name'])->asArray()->all() : [],
            'buildings' => \Yii::$app->request->post('complexId') ? Newbuilding::find()->where(['newbuilding_complex_id' => \Yii::$app->request->post('complexId')])->select(['id', 'name'])->asArray()->all() : [],
            'entrances' => \Yii::$app->request->post('buildingId') ? Entrance::find()->where(['newbuilding_id' => \Yii::$app->request->post('buildingId')])->select(['id', 'name'])->asArray()->all() : [],
            'flats' => \Yii::$app->request->post('entranceId') ? Flat::find()->where(['entrance_id' => \Yii::$app->request->post('entranceId')])->andWhere(['status' => 0])->select(['id', 'number', 'floor'])->orderBy(['number' => SORT_ASC])->asArray()->all() : [],
            'selectedParams' => [
                'developerId' => \Yii::$app->request->post('developerId') ? \Yii::$app->request->post('developerId') : '',
                'complexId' => \Yii::$app->request->post('complexId') ? \Yii::$app->request->post('complexId') : '',
                'buildingId' => \Yii::$app->request->post('buildingId') ? \Yii::$app->request->post('buildingId') : '',
                'entranceId' => \Yii::$app->request->post('entranceId') ? \Yii::$app->request->post('entranceId') : '',
            ],
        ]);   
    }

    /** Store operation to application history */
    private function storeToHistory($historyForm, $applicationModel, $applicationStatus)
    {
        $historyForm->application_id = $applicationModel->id;
        $historyForm->user_id = \Yii::$app->user->id;
        $historyForm->action = $applicationStatus;
        $applicationHistoryModel = (new ApplicationHistory())->fill($historyForm->attributes);
        $applicationHistoryModel->save();
    }

    /** Send notification */
    private function sendNotification($notificationForm, $direction, $topic, $body, $actionText, $actionUrl, $recieptId = false)
    {
        $type = 0;
        $recipient_group = '';
        
        switch ($direction) {
            case Notification::DEVELOPER_TO_ADMINS:
            case Notification::USER_TO_ADMINS:
                $type = Notification::TYPE_GROUP;
                $recipient_group = 'admin';
                break;
            case Notification::DEVELOPER_TO_USER:
            case Notification::ADMIN_TO_USER:
                $type = Notification::TYPE_INDIVIDUAL;
                break;
        }

        $notificationForm->initiator_id = \Yii::$app->user->id;
        $notificationForm->type = $type;
        if ($type === 1 && !empty($recieptId)) {
            $notificationForm->recipient_id = $recieptId;
        } elseif ($type === 2) {
            $notificationForm->recipient_group = $recipient_group;
        }
        $notificationForm->topic = $topic;
        $notificationForm->body = $body;
        $notificationForm->action_text = $actionText;
        $notificationForm->action_url = $actionUrl;
        $notificationModel = (new Notification())->fill($notificationForm->attributes);              
        $notificationModel->save();
    }
}