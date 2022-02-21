<?php

namespace app\controllers\admin\user;

use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use app\models\Agency;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

/**
 * UserController implements the CRUD actions for agency's manager.
 */
class AgencyManagerController extends Controller
{   
    public $layout = 'admin';

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
                        'actions' => ['create', 'update', 'delete', 'index'],
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
            'create' => [
                'class' => 'app\components\actions\user\CreateAgencyUser',
                'message' => 'Добавлен администратор агенства',
                'role' => 'manager',
                'redirectUrl' => 'index',
                'redirectParameter' => 'agencyId'
            ],
            'update' => [
                'class' => 'app\components\actions\user\UpdateAgencyUser',
                'message' => 'Администратор агенства обновлен',
                'redirectUrl' => 'index',
                'redirectParameter' => 'agencyId'
            ],
            'delete' => [
                'class' => 'app\components\actions\user\DeleteAgencyUser',
                'message' => 'Администратор агенства удален',
                'redirectUrl' => 'index',
                'redirectParameter' => 'agencyId'
            ],
        ];
    }

    public function actionIndex($agencyId) 
    {
        if (($agency = Agency::findOne($agencyId)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $agency->getManagers(),
            'pagination' => false,
            'sort' => ['attributes' => ['id'], 'defaultOrder' => ['id' => SORT_DESC]],
        ]);
        
        return $this->render('index', [
            'agency' => $agency,
            'dataProvider' => $dataProvider,
        ]);
    }
}
