<?php

namespace app\controllers\admin;

use app\components\traits\CustomRedirects;
use app\models\Developer;
use app\models\Newbuilding;
use app\models\NewbuildingComplex;
use app\models\NewsFile;
use app\models\form\ActionForm;
use app\models\form\NewsForm;
use app\models\search\ActionFlatSearch;
use app\models\service\News;
use app\models\search\NewsSearch;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * NewsController implements the CRUD actions for News model.
 */
class NewsController extends Controller
{
    use CustomRedirects;
    
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
                        'actions' => ['index', 'create', 'update', 'delete', 'search-flats'],
                        'roles' => ['admin'],
                    ]
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
                ->forNewbuildingComplex($newbuildingComplex)
                ->forDeveloper($developer)
                ->with(['newsFiles', 'newbuildingComplexes.developer']),
            'pagination' => ['pageParam' => 'news-page', 'pageSizeParam' => false, 'pageSize' => 20],
            'sort' => ['attributes' => ['created_at'], 'defaultOrder' => ['created_at' => SORT_DESC]],
        ]);
        
        // if (\Yii::$app->request->isAjax && isset(\Yii::$app->request->queryParams['news-page'])) {
        //     return $this->renderPartial('_loop', ['dataProvider' => $newsDataProvider]);
        // }
        
        $actionsDataProvider = new ActiveDataProvider([
            'query' => News::find()
                ->onlyActions()
                ->forNewbuildingComplex($newbuildingComplex)
                ->forDeveloper($developer)
                ->with(['newsFiles', 'newbuildingComplexes.developer']),
            'pagination' => ['pageParam' => 'action-page', 'pageSizeParam' => false, 'pageSize' => 20],
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
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'newsDataProvider' => $newsDataProvider,
            'actionsDataProvider' => $actionsDataProvider,
            'newbuildingComplexesDataProvider' => $newbuildingComplexesDataProvider,
            'searchObject' => $searchObject,
        ]);
    }

    /**
     * Creates a new News model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * 
     * @return mixed
     */
    public function actionCreate()
    {        
        $newsForm = new NewsForm();
        $actionForm = new ActionForm();
        $searchModel = new ActionFlatSearch();

        if (\Yii::$app->request->isPost &&
            ($newsForm->load(\Yii::$app->request->post())
            & $actionForm->load(\Yii::$app->request->post())
            & $searchModel->load(\Yii::$app->request->post())
            & $newsForm->process() & $actionForm->process())
        ) {
            $haveFlats = \Yii::$app->request->post('have_flats'); 
            try {
                if ($haveFlats) {
                    $news = News::create($newsForm->attributes, $actionForm->attributes, $searchModel); 
                }
                else {
                    $news = News::create($newsForm->attributes, $actionForm->attributes); 
                }
            } catch (\Exception $e) {
               return $this->redirectBackWhenException($e);
            }

            return $this->redirectWithSuccess(['index'], 'Добавлена ' . ($news->isAction() ? 'акция' : 'новость'));
        }

        return $this->render('create', [
            'news' => $newsForm,
            'action' => $actionForm,
            'search' => $searchModel,
            'developers' => Developer::find()->whereNewbuildingComplexesExist()->all(),
            'developersSearch' => Developer::getAllAsList(),
            'districts' => NewbuildingComplex::getAllDistrictsAsList(),
            'newbuildingComplexes' => $searchModel->newbuildingComplexes,
            'positionArray' => $searchModel->positionArray,
            'materials' => Newbuilding::getAllMaterialsAsList(),
        ]);
    }

    /**
     * Updates an existing News model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * 
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $news = $this->findModel($id);
        $newsForm = (new NewsForm())->fill($news->attributes);
        $newsForm->savedFiles = $news->newsFiles;
        $newsForm->developerId = (count($news->newbuildingComplexes)) ? $news->newbuildingComplexes[0]->developer_id : '';
        $newsForm->newbuildingComplexes = ArrayHelper::getColumn($news->getnewbuildingComplexes()->asArray()->all(), 'id');
        $newsForm->scenario = NewsForm::SCENARIO_UPDATE;

        $actionForm = new ActionForm();
        $actionForm->fill(!is_null($news->actionData) ? $news->actionData->attributes : []);

        $searchModel = new ActionFlatSearch();

        if ($news->isAction()) {
            $searchData = (array)json_decode($news->actionData->flat_filter);
            $loadData['ActionFlatSearch'] = $searchData;
            $searchModel->load($loadData);
        }

        if (\Yii::$app->request->isPost &&
            ($newsForm->load(\Yii::$app->request->post())
            & $actionForm->load(\Yii::$app->request->post())
//            & $searchModel->load(\Yii::$app->request->post())
            & $newsForm->process() & $actionForm->process())
        ) {
            if ($news->isAction() & !$searchModel->load(\Yii::$app->request->post())) {
                return $this->redirectWithError(['index'], 'Произошла ошибка. Обратитесь в службу поддержки');
            }
            try {
                $news->edit($newsForm->attributes, $actionForm->attributes, $searchModel);
            } catch (\Exception $e) {
                return $this->redirectBackWhenException($e);
            }

            return $this->redirectWithSuccess(['update', 'id' => $news->id], $news->isAction() ? 'Акция обновлена' : 'Новость обновлена');
        }

        return $this->render('update', [
            'news' => $newsForm,
            'action' => $actionForm,
            'search' => $searchModel,
            'newsId' => $news->id,
            'developers' => Developer::find()->whereNewbuildingComplexesExist()->all(),
            'developersSearch' => Developer::getAllAsList(),
            'districts' => NewbuildingComplex::getAllDistrictsAsList(),
            'newbuildingComplexes' => $searchModel->newbuildingComplexes,
            'positionArray' => $searchModel->positionArray,
            'materials' => Newbuilding::getAllMaterialsAsList(),
        ]);
    }

    /**
     * Deletes an existing News model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * 
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        try {
            $model->delete();
        } catch (\Exception $e) {
            return $this->redirectBackWhenException($e);
        }

        return $this->redirectWithSuccess(['index'], $model->isAction() ? 'Акция удалена' : 'Новость удалена');
    }
 
    /**
     * Finds the News model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * 
     * @param integer $id
     * @return News the loaded model
     * @throws NotFoundHttpException if the model cannot be found     */
    protected function findModel($id)
    {
        if (($model = News::findOne($id)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }

        return $model;
    }

     public function actionSearchFlats()
    {
        $queryParams = \Yii::$app->request->post();

        $urlParams['AdvancedFlatSearch'] = $queryParams['ActionFlatSearch'];
        $searchUrl = '/site/search/?' . http_build_query($urlParams);

        $searchModel = new ActionFlatSearch();

        list($result, $resultCode) = $searchModel->search($queryParams);

        if ($resultCode == ActionFlatSearch::ERROR_HAVE_DISCOUNT) {
            return $this->renderPartial('_flat_result', ['errorText' => 'В выборке обнаружены квартиры со скидкой, измените параметры поиска']);
        }
        else {
            return $this->renderPartial('_flat_result', ['dataProvider' => $result, 'searchUrl' => $searchUrl]);
        }

    }
}
