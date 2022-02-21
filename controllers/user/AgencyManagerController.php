<?php

namespace app\controllers\user;

use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

/**
 * UserController implements the CRUD actions for agency's manager.
 */
class AgencyManagerController extends Controller
{   
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create', 'update', 'delete'],
                        'roles' => ['admin'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['view'],
                        'roles' => ['@'],
                    ],
                ]
            ],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'view' => [
                'class' => 'app\components\actions\user\ViewAgencyUser',
                'view' => '/user/agency-manager/view',
            ],
            'create' => [
                'class' => 'app\components\actions\user\CreateAgencyUser',
                'message' => 'Добавлен администратор агенства',
                'role' => 'manager',
                'redirectUrl' => 'agency/view',
                'redirectParameter' => 'id'
            ],
            'update' => [
                'class' => 'app\components\actions\user\UpdateAgencyUser',
                'message' => 'Администратор агенства обновлен',
                'redirectUrl' => 'user/agency-manager/view',
                'redirectParameter' => 'id'
            ],
            'delete' => [
                'class' => 'app\components\actions\user\DeleteAgencyUser',
                'message' => 'Администратор агенства удален',
                'redirectUrl' => 'agency/view',
                'redirectParameter' => 'id'
            ],
        ];
    }
}
