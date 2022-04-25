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
                    'index' => ['GET'],
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
        } else {
            $tickets = $model->getTicketsByAuthor(\Yii::$app->user->id); 
        }
         
        return $this->render('index', [
            'user' => \Yii::$app->user->identity,
            'tickets' => $tickets
        ]);
    }
}