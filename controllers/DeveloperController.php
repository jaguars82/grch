<?php

namespace app\controllers;

use app\components\traits\CustomRedirects;
use app\models\service\Developer;
use app\models\form\ImportForm;
use app\models\search\DeveloperSearch;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use tebe\inertia\web\Controller;
use yii\web\NotFoundHttpException;
use app\components\SharedDataFilter;

/**
 * DeveloperController implements the CRUD actions for Developer model.
 */
class DeveloperController extends Controller
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
                    'get-developers' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'get-developers'],
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
                'searchModelClass' => DeveloperSearch::classname(),
                'view' => 'Developer/Index'
            ],
        ];
    }

    /**
     * Displays a single Developer model.
     * 
     * @param integer $id developer ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {       
        $model = $this->findModel($id);
        
        $newbuildingComplexDataProvider = new ActiveDataProvider([
            'query' => $model->getNewbuildingComplexes()->onlyActive()->onlyWithActiveBuildings()->with(['flats']),
            'pagination' => false,
            'sort' => ['attributes' => ['id'], 'defaultOrder' => ['id' => SORT_DESC]],
        ]);
        
        $contactDataProvider = new ActiveDataProvider([
            'query' => $model->getContacts(),
            'pagination' => false,
            'sort' => ['attributes' => ['id'], 'defaultOrder' => ['id' => SORT_DESC]],
        ]);

        $newsDataProvider = new ActiveDataProvider([
            'query' => $model->getNews()->limit(4),
            'pagination' => false,
            'sort' => ['attributes' => ['created_at'], 'defaultOrder' => ['created_at' => SORT_DESC]],
        ]);

        $officeDataProvider = new ActiveDataProvider([
            'query' => $model->getOffices(),
            'pagination' => false,
            'sort' => ['attributes' => ['id'], 'defaultOrder' => ['id' => SORT_DESC]], 
        ]);
        
        return $this->inertia('Developer/View', [
            'developer' => ArrayHelper::toArray($model),
            'newbuildingComplexDataProvider' => ArrayHelper::toArray($newbuildingComplexDataProvider->getModels()),
            'newsDataProvider' => ArrayHelper::toArray($newsDataProvider->getModels()),
            'contactDataProvider' => ArrayHelper::toArray($contactDataProvider->getModels()),
            'officeDataProvider' => ArrayHelper::toArray($officeDataProvider->getModels())
        ]);
    }

    /**
     * Get list of developers in JSON format
     */
    public static function actionGetDevelopers()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;        
        \Yii::$app->response->data = Developer::getAllAsList();
    }

    /**
     * Finds the Developer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * 
     * @param integer $id developer ID
     * @return Developer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Developer::findOne($id)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }

        return $model;
    }
}
