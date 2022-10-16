<?php

namespace app\controllers\user;

use app\components\traits\CustomRedirects;
use app\components\SharedDataFilter;
use app\models\Commerciai;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use tebe\inertia\web\Controller;
use yii\helpers\ArrayHelper;

class CommercialController extends Controller
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
        $model = new Commercial();

        $commercials = '';

        return $this->inertia('User/Commercial/Index', [
            'user' => \Yii::$app->user->identity,
            'commercials' => ArrayHelper::toArray($commercials),
        ]);
    }

    public function actionView($id)
    {
        $commercial = (new Commercial())->findOne($id);
       
        return $this->inertia('User/Commercial/View', [
            'commercial' => ArrayHelper::toArray($commercial),
        ]);
    }
}