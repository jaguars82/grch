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
                    'index' => ['GET'],
                    'view' => ['GET'],
                    'create' => ['GET', 'POST']
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

    public function actionView() {

        $ticketId = \Yii::$app->request->get('id');

        //$ticket_model = new SupportTicket();
        
        // $ticket = $ticket_model->findOne($ticketId);
        $ticket = (new SupportTicket())->findOne($ticketId);
        $messages = $ticket->messages;

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
         * Fill some hidden attributes
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
            ($ticket_model->load(\Yii::$app->request->post())
            /*& $ticket_model->process()*/ && $message_model->load(\Yii::$app->request->post()) /*& $message_model->process()*/)
        ) {
            try {
                $transaction = \Yii::$app->db->beginTransaction();

                try {

                    $ticket = (new SupportTicket())->fill($ticket_model->attributes);
                    $ticket->save();

                    $message_model->ticket_id = $ticket->id;
                    $message_model->message_number = (new SupportMessage())->getMessagesAmount($message_model->ticket_id) + 1;

                    $message = (new SupportMessage())->fill($message_model->attributes);
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
