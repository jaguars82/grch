<?php

namespace app\controllers\user;

use app\components\traits\CustomRedirects;
use app\models\SupportTicket;
use app\models\User;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

class SupportController extends Controller
{
    use CustomRedirects;

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['GET', 'POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['admin', 'manager', 'agent'],
                    ],
                ]
            ],
        ];
    }

    public function actionIndex()
    {
        $model = new SupportTicket();

        if(\Yii::$app->user->can('admin')) {
            $tickets = $model->getAllTickets();

            foreach($tickets as $key => $ticket) {
                $ticket->setUnreadFromAuthor();
                $ticket->setAuthorName();
                $ticket->setAuthorSurname();
                $ticket->setAuthorAvatar();
                $ticket->setAuthorRole();
                $ticket->setAuthorAgency();
            }            
        } else {
            $tickets = $model->getTicketsByAuthor(\Yii::$app->user->id);

            foreach($tickets as $key => $ticket) {
                $ticket->setUnreadFromAdmin();
            }
        }
        
        return $this->render('index', [
            'user' => \Yii::$app->user->identity,
            'tickets' => $tickets
        ]);

        /*
        if(\Yii::$app->request->post('action') == 'refresh_ticket') {
            return $this->renderPartial('index', [
                'user' => \Yii::$app->user->identity,
                'tickets' => $tickets
            ]);
        } else {
            return $this->render('index', [
                'user' => \Yii::$app->user->identity,
                'tickets' => $tickets
            ]);
        }
        */
    }
}