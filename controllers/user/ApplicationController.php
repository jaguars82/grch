<?php

namespace app\controllers\user;

use app\components\traits\CustomRedirects;
use app\components\SharedDataFilter;
use app\models\User;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use tebe\inertia\web\Controller;

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
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index'],
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
        //$model = new SupportTicket();

        /*if(\Yii::$app->user->can('admin')) {
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
        }*/
        
        return $this->inertia('User/Application/Index', [
            'user' => \Yii::$app->user->identity,
        ]);
    }
}