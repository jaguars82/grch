<?php

namespace app\controllers\user;

use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

/**
 * UserController implements the CRUD actions for agency's agent.
 */
class AgencyAgentController extends Controller
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
                'view' => '/user/agency-agent/view',
            ],
            'create' => [
                'class' => 'app\components\actions\user\CreateAgencyUser',
                'message' => 'Добавлен агент',
                'isCheckCurrentUser' => true,
                'role' => 'agent',
                'redirectUrl' => 'agency/view',
                'redirectParameter' => 'id'
            ],
            'update' => [
                'class' => 'app\components\actions\user\UpdateAgencyUser',
                'message' => 'Администратор агенства обновлен',
                'isCheckCurrentUser' => true,
                'redirectUrl' => 'user/agency-agent/view',
                'redirectParameter' => 'id'
            ],
            'delete' => [
                'class' => 'app\components\actions\user\DeleteAgencyUser',
                'message' => 'Агент удален',
                'isCheckCurrentUser' => true,
                'redirectUrl' => 'agency/view',
                'redirectParameter' => 'id'
            ],
        ];
    }
}
