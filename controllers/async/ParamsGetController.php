<?php

namespace app\controllers\async;

use app\models\SupportTicket;
use app\components\async\ParamsGet;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
// use yii\web\Controller;
use tebe\inertia\web\Controller;

class ParamsGetController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'event' => ['POST', 'GET'],
                    'support-messages' => ['POST', 'GET'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['event', 'support-messages'],
                        'roles' => ['admin', 'manager', 'agent', 'developer_repres'],
                    ],
                ]
            ],
        ];
    }

    /**
     * Get any events
     */
    public function actionEvent() {
        $params = (new ParamsGet())->getAllEventsParams();

        return $this->renderPartial('/widgets/status-indicator', [
            'url' => \Yii::$app->request->post('url'),
            'action' => \Yii::$app->request->post('action'),
            'status' => $params['status'],
            'amount' => $params['amount']
        ]);
    }

    /** Support messages for old frontend - DELETE LATER */
    public function actionSupportMessages()
    {
        $messages_amount = (new ParamsGet())->getSupportMessagesAmount();

        return $this->renderPartial('/widgets/status-indicator', [
            'url' => \Yii::$app->request->post('url'),
            'action' => \Yii::$app->request->post('action'),
            'status' => $messages_amount > 0 ? true : false,
            'amount' => $messages_amount
        ]);
    }
}