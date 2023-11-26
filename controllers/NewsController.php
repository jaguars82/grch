<?php

namespace app\controllers;

use app\components\traits\CustomRedirects;
use app\models\Developer;
use app\models\NewbuildingComplex;
use app\models\NewsFile;
use app\models\form\ActionForm;
use app\models\form\NewsForm;
use app\models\service\News;
use app\models\search\NewsSearch;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use tebe\inertia\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * NewsController implements the CRUD actions for News model.
 */
class NewsController extends Controller
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
                        'actions' => ['create', 'update', 'delete'],
                        'roles' => ['admin'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'download'],
                        'roles' => ['@'],
                    ],
                ]
            ],
        ];
    }

    /**
     * Lists all News models.
     * 
     * @return mixed
     */
    public function actionIndex()
    {
        $data = \Yii::$app->request->queryParams;
        $searchModel = new NewsSearch(); 
        $dataProvider = $searchModel->search($data);
        
        // if (\Yii::$app->request->isAjax && isset(\Yii::$app->request->queryParams['all-page'])) {
        //     return $this->renderPartial('_loop', ['dataProvider' => $dataProvider]);
        // }        

        $newbuildingComplex = isset($data['newbuilding-complex']) ? $data['newbuilding-complex'] : null;
        $developer = isset($data['developer']) ? $data['developer'] : null;        

        $newsDataProvider = new ActiveDataProvider([
            'query' => News::find()
                ->onlyNews()
                ->onlyActual()
                ->forNewbuildingComplex($newbuildingComplex)
                ->forDeveloper($developer)
                ->with(['newsFiles', 'newbuildingComplexes.developer']),
            'pagination' => ['pageParam' => 'news-page', 'pageSizeParam' => false, 'pageSize' => 5],
            'sort' => ['attributes' => ['created_at'], 'defaultOrder' => ['created_at' => SORT_DESC]],
        ]);
        
        // if (\Yii::$app->request->isAjax && isset(\Yii::$app->request->queryParams['news-page'])) {
        //     return $this->renderPartial('_loop', ['dataProvider' => $newsDataProvider]);
        // }

        $actionsDataProvider = new ActiveDataProvider([
            'query' => News::find()
                ->onlyActions()
                ->onlyActual()
                ->forNewbuildingComplex($newbuildingComplex)
                ->forDeveloper($developer)
                ->with(['newsFiles', 'newbuildingComplexes.developer']),
            'pagination' => ['pageParam' => 'action-page', 'pageSizeParam' => false, 'pageSize' => 5],
            'sort' => ['attributes' => ['created_at'], 'defaultOrder' => ['created_at' => SORT_DESC]],
        ]);

        // if (\Yii::$app->request->isAjax && isset(\Yii::$app->request->queryParams['action-page'])) {
        //     return $this->renderPartial('_loop', ['dataProvider' => $actionsDataProvider]);
        // }

        $newbuildingComplexesDataProvider = new ActiveDataProvider([
            'query' => Developer::find()->whereNewbuildingComplexesExist()->with('newbuildingComplexes'),
            'pagination' => false,
            'sort' => ['attributes' => ['name'], 'defaultOrder' => ['name' => SORT_ASC]],
        ]);

        if (!is_null($developer)) {
            $searchObject = Developer::findOne($developer)->name;
        } elseif (!is_null($newbuildingComplex)) {
            $searchObject = NewbuildingComplex::findOne($newbuildingComplex)->name;
        } else {
            $searchObject = null;
        }

        // echo '<pre>'; var_dump($dataProvider->getModels()); echo '</pre>'; die;

        /*return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'newsDataProvider' => $newsDataProvider,
            'actionsDataProvider' => $actionsDataProvider,
            'newbuildingComplexesDataProvider' => $newbuildingComplexesDataProvider,
            'searchObject' => $searchObject,
        ]);*/

        $postModels = $dataProvider->getModels();

        return $this->inertia('News/Index', [
            'posts' => ArrayHelper::toArray($postModels),
            'pagination' => [
                'page' => $dataProvider->getPagination()->getPage(),
                'totalPages' => $dataProvider->getPagination()->getPageCount()
            ]
        ]);
    }

    /**
     * Displays a single News model.
     * 
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
    
    /**
     * Getting file for given news.
     * 
     * @param integer $id News's ID
     * @param integer $file News file origin name
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDownload($id, $file)
    {
        if (($newsFile = NewsFile::find()->where(['news_id' => (int)$id, 'saved_name' => $file])->one()) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }       
        
        return \Yii::$app->response->sendFile(\Yii::getAlias("@webroot/uploads/{$newsFile->saved_name}"), $newsFile->name);
    }

    /**
     * Finds the News model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * 
     * @param integer $id
     * @return News the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = News::findOne($id)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }

        return $model;
    }
}
