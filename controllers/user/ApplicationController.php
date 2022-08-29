<?php

namespace app\controllers\user;

use app\components\traits\CustomRedirects;
use app\components\SharedDataFilter;
use app\models\User;
use app\models\Application;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use tebe\inertia\web\Controller;
use yii\helpers\ArrayHelper;

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
                    'view' => ['GET', 'POST']
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
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
        $model = new Application();

        if(\Yii::$app->user->can('admin')) {
            $applications = $model->activeApplications;    
        }
        
        return $this->inertia('User/Application/Index', [
            'user' => \Yii::$app->user->identity,
            'applications' => ArrayHelper::toArray($applications)
        ]);
    }

    public function actionView($id)
    {
        $application = (new Application())->findOne($id);
        
        return $this->inertia('User/Application/View', [
            'application' => ArrayHelper::toArray($application)
        ]);
    }
}