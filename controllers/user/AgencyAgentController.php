<?php

namespace app\controllers\user;

use yii\filters\AccessControl;
use app\components\SharedDataFilter;
use yii\filters\VerbFilter;
//use yii\web\Controller;
use tebe\inertia\web\Controller;
use app\models\Agency;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

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
                    'delete' => ['GET', 'POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'create', 'update', 'delete'],
                        'roles' => ['admin', 'manager'],
                        'matchCallback' => function () {
                            if(\Yii::$app->user->can('admin')) return true;
                            // ID агентства пользователя с ролью 'manager' должно совпадать с ID редактируемого агентства
                            return $_GET['agencyId'] == \Yii::$app->user->identity->agency_id;
                        },
                    ],
                ]
            ],
            [
                'class' => SharedDataFilter::class
            ],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'create' => [
                'class' => 'app\components\actions\user\CreateAgencyUser',
                'message' => 'Добавлен агент',
                'isCheckCurrentUser' => true,
                'role' => 'agent',
                'redirectUrl' => 'user/agency-agent/index',
                'redirectParameter' => 'agencyId',
            ],
            'update' => [
                'class' => 'app\components\actions\user\UpdateAgencyUser',
                'message' => 'Профиль агента обновлён',
                'isCheckCurrentUser' => true,
                'redirectUrl' => 'user/agency-agent/index',
                'redirectParameter' => 'agencyId',
            ],
            'delete' => [
                'class' => 'app\components\actions\user\DeleteAgencyUser',
                'message' => 'Агент удален',
                'isCheckCurrentUser' => true,
                'redirectUrl' => 'user/agency-agent/index',
                'redirectParameter' => 'agencyId',
            ],
        ];
    }

    public function actionIndex($agencyId) 
    {
        if (($agency = Agency::findOne($agencyId)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }

        return $this->inertia('User/AgencyAgent/Index', [
            'agency' => ArrayHelper::toArray($agency),
            'agents' => ArrayHelper::toArray($agency->agents),
        ]);
    }
}
