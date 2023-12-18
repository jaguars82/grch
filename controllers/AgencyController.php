<?php

namespace app\controllers;

use app\components\exceptions\AppException;
use app\components\traits\CustomRedirects;
use app\models\Agency;
use app\models\form\AgencyForm;
use app\models\search\AgencySearch;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use tebe\inertia\web\Controller;
use app\components\SharedDataFilter;

/**
 * AgencyController implements the CRUD actions for Agency model.
 */
class AgencyController extends Controller
{
    use CustomRedirects;
    
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
                        'actions' => ['index', 'view'],
                        'roles' => ['@'],
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
            'index' => [
                'class' => 'app\components\actions\IndexWithSearch',
                'searchModelClass' => AgencySearch::classname(),
                'view' => 'Agency/Index'
            ],
        ];
    }

    /**
     * Displays a single Agency model.
     * 
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id = 0)
    {        
        $model = $this->findModel($id);
        
        $contactDataProvider = new ActiveDataProvider([
            'query' => $model->getContacts(),
            'pagination' => false,
            'sort' => ['attributes' => ['id'], 'defaultOrder' => ['id' => SORT_DESC]],
        ]);
        
        $managerDataProvider = new ActiveDataProvider([
            'query' => $model->getManagers(),
            'pagination' => false,
            'sort' => ['attributes' => ['id'], 'defaultOrder' => ['id' => SORT_DESC]],
        ]);
        
        $agentDataProvider = new ActiveDataProvider([
            'query' => $model->getAgents(),
            'pagination' => false,
            'sort' => ['attributes' => ['id'], 'defaultOrder' => ['id' => SORT_DESC]],
        ]);
        
        return $this->inertia('Agency/View', [
            'agency' => ArrayHelper::toArray($model),
            'contactDataProvider' => ArrayHelper::toArray($contactDataProvider->getModels()),
            'managerDataProvider' => ArrayHelper::toArray($managerDataProvider->getModels()),
            'agentDataProvider' => ArrayHelper::toArray($agentDataProvider->getModels()),
        ]);
    }
    
    /**
     * Finds the Agency model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * 
     * @param integer $id
     * @return Agency the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Agency::findOne($id)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }

        return $model;
    }
}
