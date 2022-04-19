<?php

namespace app\controllers\user;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class SupportTicketController extends \yii\web\Controller
{

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['GET'],
                    'create' => ['GET', 'POST']
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'create'],
                        'roles' => ['admin', 'manager', 'agent'],
                    ],
                ]
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionCreate()
    {

        $model = new \app\models\SupportTickets();

        if ($model->load(\Yii::$app->request->post())) {
            if ($model->validate()) {
                // form inputs are valid, do something here
                return;
            }
        }
    
        return $this->render('_form', [
            'model' => $model,
        ]);

        // return $this->render('create');
    }
}
