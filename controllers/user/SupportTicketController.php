<?php

namespace app\controllers\user;

use app\models\SupportTicket;
use app\models\SupportMessage;
use app\models\form\SupportTicketForm;
use app\models\form\SupportMessageForm;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\components\traits\CustomRedirects;
use app\components\SharedDataFilter;
use tebe\inertia\web\Controller;
use yii\helpers\ArrayHelper;

class SupportTicketController extends Controller
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
                    'create' => ['GET', 'POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'create'],
                        'roles' => ['admin', 'manager', 'agent', 'developer_repres'],
                    ],
                ]
            ],
            [
                'class' => SharedDataFilter::class
            ],        
        ];
    }

    public function actionView($id) {

        $ticket = (new SupportTicket())->findOne($id);

        $message_form = new SupportMessageForm();

        /**
         * refresh messages in support chat via pjax
         */
        if (\Yii::$app->request->isPost && \Yii::$app->request->post('action') === 'refresh') {
        
            //$ticketId = \Yii::$app->request->post('id');

            $ticket = (new SupportTicket())->findOne($id);
            $messages = $ticket->messages;

            foreach($messages as $key => $message) {

                $message->setAuthorName();
                $message->setAuthorSurname();
                $message->setAuthorAvatar();
                $message->setAuthorRole();
                $message->setAuthorAgency();

                if(\Yii::$app->user->can('admin')){
                    if($message->authorRole != 'Администратор') {
                        // устанавливаем в базе 'seen_by_interlocuter' = 1
                        $current_message = (new SupportMessage)->findOne($message->id);
                        $current_message->seen_by_interlocutor = 1;
                        $current_message->save();
                    }
                } else {
                    if($message->authorRole == 'Администратор') {
                        // устанавливаем в базе 'seen_by_interlocuter' = 1 
                        $current_message = (new SupportMessage)->findOne($message->id);
                        $current_message->seen_by_interlocutor = 1;
                        $current_message->save();
                    }
                }
            }
            
            return $this->renderPartial('view', [
                'ticket' => $ticket,
                'messages' => $messages,
            ]);
        }

        /** Create new message in support chat */
        if (\Yii::$app->request->isPost && 
        ($message_form->load(\Yii::$app->request->post(), '')
        /*& $message_form->process()*/)
        ) {
            try {
                $transaction = \Yii::$app->db->beginTransaction();

                try {

                    $message_form->ticket_id = $ticket->id;
                    $message_form->author_id = \Yii::$app->user->id;
                    $message_form->author_role = array_key_first(\Yii::$app->authManager->getRolesByUser(\Yii::$app->user->id));
                    $message_form->message_number = (new SupportMessage())->getMessagesAmount($message_form->ticket_id) + 1;

                    $message = (new SupportMessage())->fill($message_form->attributes);
                    $message->seen_by_interlocutor = 0;
                    $message->save();
                    
                    // renew 'has_unread_messages' status of the ticket when a new message add
                    if(\Yii::$app->user->can('admin')) {
                        $ticket->has_unread_messages_from_support = 1;
                    } else {
                        $ticket->has_unread_messages_from_author = 1;
                    }
                    $ticket->save();
        
                    $transaction->commit();
                } catch(\Exception $e) {
                    $transaction->rollBack();
                    throw $e;
                }
                
            } catch (\Exception $e) {
                return $this->redirectBackWhenException($e);
            }
        }

        $messages = $ticket->messages;

        /** 
         * Prepare messages array for Vue component
        */
        $messages_array = array();
        foreach ($messages as $message) {
            $message_entry = ArrayHelper::toArray($message);
            $message_entry['author'] = ArrayHelper::toArray($message->author);
            $message_entry['author']['roleLabel'] = $message->author->roleLabel;
            if(!empty($message->author->agency_id)) {
                $message_entry['author']['agency_name'] = $message->author->agency->name;
            }
            array_push($messages_array, $message_entry);
        }

        return $this->inertia('User/SupportTicket/View', [
            'ticket' => ArrayHelper::toArray($ticket),
            'messages' => $messages_array,
        ]);
    }

    public function actionCreate()
    {
        $ticket_model = new SupportTicketForm();
        $message_model = new SupportMessageForm();

        /**
         * Fill some initial attributes
         */
        $ticket_model->author_id = $message_model->author_id = \Yii::$app->user->id;
        $message_model->author_role = array_key_first(\Yii::$app->authManager->getRolesByUser(\Yii::$app->user->id));

        /*
        if ($ticket_model->load(\Yii::$app->request->post())) {
            if ($ticket_model->validate()) {
                // form inputs are valid, do something here
                return;
            }
        }
        */

        if (\Yii::$app->request->isPost && 
            $ticket_model->load(\Yii::$app->request->post(), '')
            /*& $ticket_model->process()*/ && $message_model->load(\Yii::$app->request->post(), '') /*& $message_model->process()*/)
         {
            try {
                $transaction = \Yii::$app->db->beginTransaction();

                try {

                    $ticket = (new SupportTicket())->fill($ticket_model->attributes);
                    $ticket->ticket_number = \Yii::$app->user->id.'-#'.((new SupportTicket())->getTicketsAmountByAuthor(\Yii::$app->user->id) + 1);
                    $ticket->is_archived = 0;
                    $ticket->is_closed = 0;
                    $ticket->has_unread_messages_from_support = 0;
                    $ticket->has_unread_messages_from_author = 1;
                    $ticket->save();

                    $message_model->ticket_id = $ticket->id;
                    $message_model->message_number = (new SupportMessage())->getMessagesAmount($message_model->ticket_id) + 1;

                    $message = (new SupportMessage())->fill($message_model->attributes);
                    $message->seen_by_interlocutor = 0;
                    $message->save();        
        
                    $transaction->commit();
                } catch(\Exception $e) {
                    $transaction->rollBack();
                    throw $e;
                }

            } catch (\Exception $e) {
                return $this->redirectBackWhenException($e);
            }

            //return $this->redirectWithSuccess(['/user/support/index'], 'Ваш запрос отправлен в службу поддержки');
            return $this->redirect(['/user/support/index']);
        }

        return $this->inertia('User/SupportTicket/Create', [
            'tickets_amount' => (new SupportTicket())->getTicketsAmountByAuthor(\Yii::$app->user->id),
        ]);
    }
}
