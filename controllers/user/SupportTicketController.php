<?php

namespace app\controllers\user;

use app\models\SupportTicket;
use app\models\SupportMessage;
use app\models\form\SupportTicketForm;
use app\models\form\SupportMessageForm;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\components\traits\CustomRedirects;

class SupportTicketController extends \yii\web\Controller
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
                        'roles' => ['admin', 'manager', 'agent'],
                    ],
                ]
            ],
        ];
    }

    /* public function actionIndex() {

        if (\Yii::$app->request->isPost && \Yii::$app->request->post('action') === 'refresh_ticket') {

            $ticketId = \Yii::$app->request->post('id');

            $ticket = (new SupportTicket())->findOne($ticketId);

            return $this->renderPartial('view', [
                'ticket' => $ticket,
                'messages' => false,
            ]);
        }
    } */

    public function actionView() {

        $ticketId = \Yii::$app->request->get('id');

        $ticket = (new SupportTicket())->findOne($ticketId);
        $messages = $ticket->messages;

        foreach($messages as $key => $message) {
            $message->setAuthorName();
            $message->setAuthorSurname();
            $message->setAuthorAvatar();
            $message->setAuthorRole();
            $message->setAuthorAgency();
        }

        $message_form = new SupportMessageForm();

        /**
         * refresh messages in support chat via pjax
         */
        if (\Yii::$app->request->isPost && \Yii::$app->request->post('action') === 'refresh') {
        
            $ticketId = \Yii::$app->request->post('id');

            $ticket = (new SupportTicket())->findOne($ticketId);
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
        ($message_form->load(\Yii::$app->request->post())
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
            return $this->redirectWithSuccess(\Yii::$app->request->referrer, 'Сообщение отправлено');
        }

        return $this->render('view', [
            'ticket' => $ticket,
            'messages' => $messages,
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
        // $ticket_model->is_archived = $message_model->seen_by_interlocutor = 0;
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
            ($ticket_model->load(\Yii::$app->request->post())
            /*& $ticket_model->process()*/ && $message_model->load(\Yii::$app->request->post()) /*& $message_model->process()*/)
        ) {
            try {
                $transaction = \Yii::$app->db->beginTransaction();

                try {

                    $ticket = (new SupportTicket())->fill($ticket_model->attributes);
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

                // return $ticket;

            } catch (\Exception $e) {
                return $this->redirectBackWhenException($e);
            }

            return $this->redirectWithSuccess(['/user/support/index'], 'Ваш запрос отправлен в службу поддержки');
        }

        return $this->render('create', [
            'tickets_amount' => (new SupportTicket())->getTicketsAmountByAuthor(\Yii::$app->user->id),
            'ticket_model' => $ticket_model,
            'message_model' => $message_model,
        ]);
    }
}
