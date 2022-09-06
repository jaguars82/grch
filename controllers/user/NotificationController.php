<?php

namespace app\controllers\user;

use app\components\traits\CustomRedirects;
use app\components\SharedDataFilter;
use app\models\Notification;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use tebe\inertia\web\Controller;
use yii\helpers\ArrayHelper;

class NotificationController extends Controller
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
        $model = new Notification();

        if (\Yii::$app->user->can('admin')) {
            $notifications = $model->notificationsForAdmin;    
        } else {
            $notifications = $model->getNotificationsForUser(\Yii::$app->user->id)->all();
        }
        
        return $this->inertia('User/Notification/Index', [
            'user' => \Yii::$app->user->identity,
            'notifications' => ArrayHelper::toArray($notifications),
        ]);
    }

    public function actionView($id)
    {
        $notification = (new Notification())->findOne($id);
        
        return $this->inertia('User/Notification/View', [
            'notification' => ArrayHelper::toArray($notification),
        ]);
    }
}