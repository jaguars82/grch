<?php

namespace app\controllers\async;

use app\models\SupportTicket;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

class ParamsGetController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'event' => ['POST', 'GET'],
                    'supportmessages' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['event', 'supportmessages'],
                        'roles' => ['admin', 'manager', 'agent'],
                    ],
                ]
            ],
        ];
    }

    /**
     * Get any events
     */
    public function actionEvent() {
        $status = false;
        $amount = 0;

        // check support messages
        if($support_messages_amount = $this->actionSupportmessages() > 0) { 
            $amount = $amount + $support_messages_amount;
            $status = true;
        }

        return $this->renderPartial('/widgets/status-indicator', [
            'status' => $status,
            'amount' => $amount
        ]);
    }

    /**
     * Get if there are uread support messages
     */
    public function actionSupportmessages() {
        $ticket_model = new SupportTicket();

        if(\Yii::$app->user->can('admin')) {
            $result = $ticket_model->getTicketsWithUnreadFromAuthor();
        } else {
            $result = $ticket_model->getTicketsWithUnreadFromAdmin(\Yii::$app->user->id);
        }
        return $result;
    }

}