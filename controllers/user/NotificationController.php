<?php

namespace app\controllers\user;

use app\components\traits\CustomRedirects;
use app\components\SharedDataFilter;
use app\models\Notification;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use tebe\inertia\web\Controller;
use yii\helpers\ArrayHelper;
use yii\data\Pagination;

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
        $query = Notification::find();
        if (\Yii::$app->user->can('admin')) {
            $query->andWhere(['recipient_group' => 'admin']);
        } elseif (\Yii::$app->user->identity->role === 'developer_repres') {
            $query->andWhere(['recipient_group' => 'developer_repres']);
        } else {
            $query->andWhere(['recipient_id' => \Yii::$app->user->id]);
        }

        // get the total number of notifications
        $count = $query->count();

        // create a pagination object with the total count
        $pagination = new Pagination(['totalCount' => $count]);

        if (!empty(\Yii::$app->request->get('psize'))) {
            $pagination->setPageSize(\Yii::$app->request->get('psize'));
        }

        $notifications = $query
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        return $this->inertia('User/Notification/Index', [
            'user' => \Yii::$app->user->identity,
            'notifications' => ArrayHelper::toArray($notifications),
            'totalRows' => $count,
            'page' => $pagination->page,
            'psize' => $pagination->pageSize,
        ]);
    }

    public function actionView($id)
    {
        $notification = (new Notification())->findOne($id);

        /** match notification seen by recipient */
        if ($notification->seen_by_recipient == 0) {
            if (\Yii::$app->user->can('admin')) {
                if ($notification->recipient_group == 'admin') {
                    $notification->seen_by_recipient = 1;
                    $notification->save();
                }
            } elseif (\Yii::$app->user->identity->role === 'developer_repres') {
                if ($notification->recipient_group == 'developer_repres') {
                    $notification->seen_by_recipient = 1;
                    $notification->save();
                }
            } else {
                if ($notification->recipient_id == \Yii::$app->user->identity->id) {
                    $notification->seen_by_recipient = 1;
                    $notification->save();
                }
            }
        }
        
        return $this->inertia('User/Notification/View', [
            'notification' => ArrayHelper::toArray($notification),
        ]);
    }
}