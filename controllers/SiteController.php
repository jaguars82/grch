<?php

namespace app\controllers;

use app\models\Developer;
use app\models\Agency;
use app\models\Bank;
use app\models\Newbuilding;
use app\models\NewbuildingComplex;
use app\models\News;
use app\models\search\AdvancedFlatSearch;
use app\models\search\MapFlatSearch;
use app\models\search\SimpleFlatSearch;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
//use tebe\inertia\web\Controller;
use app\models\City;
use app\models\Region;
use app\models\District;
use yii\helpers\ArrayHelper;

/**
 * SiteController implements the actions for site.
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'search', 'map', 'logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'search', 'map'],
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {        
        $this->layout = 'fullWidth';
        
        \Yii::$app->response->cookies->add(new \yii\web\Cookie([
            'name' => 'site-index-query-string-' . \Yii::$app->user->id,
            'value' => \Yii::$app->request->queryString,
        ]));
        
        $searchModel = new AdvancedFlatSearch();
        $searchModel->scenario = AdvancedFlatSearch::SCENARIO_SIMPLE;

        $newsList = (new News())->find()->all();

        $newsDataProvider = new ActiveDataProvider([
            'query' => News::find()->onlyNews()->limit(4),
            'pagination' => false,
            'sort' => [
                'attributes' => ['created_at'],
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]);

        $actionsDataProvider = new ActiveDataProvider([
            'query' => News::find()->onlyActions()->limit(2),
            'pagination' => false,
            'sort' => [
                'attributes' => ['created_at'],
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]);

        $developerDataProvider = new ActiveDataProvider([
            'query' => Developer::find()->limit(10),
            'pagination' => false,
            'sort' => [
                'attributes' => ['sort'],
                'defaultOrder' => ['sort' => SORT_ASC],
            ],
        ]);
        
        $agencyDataProvider = new ActiveDataProvider([
            'query' => Agency::find()->limit(10),
            'pagination' => false,
            'sort' => [
                'attributes' => ['created_at'],
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]);
                
        $bankDataProvider = new ActiveDataProvider([
            'query' => Bank::find()->limit(10),
            'pagination' => false,
            'sort' => [
                'attributes' => ['id'],
                'defaultOrder' => ['id' => SORT_DESC],
            ],
        ]);

        return $this->render('index', [
        //return $this->inertia('Main/Index', [
            'searchModel' => $searchModel,
            'newsList' => $newsList,
            //'newsList' => News::find()->asArray()->all(),
            'newsDataProvider' =>$newsDataProvider,
            'actionsDataProvider' => $actionsDataProvider,
            'developerDataProvider' => $developerDataProvider,
            'agencyDataProvider' => $agencyDataProvider,
            'bankDataProvider' => $bankDataProvider,
            'districts' => District::getAllForLocationAsList(),
            'developers' => Developer::getAllAsList(),
            'newbuildingComplexes' => $searchModel->newbuildingComplexes
        ]);
    }
    
    /**
     * Advanced flat search
     * 
     * @return mixed
     */
    public function actionSearch()
    {
        \Yii::$app->response->cookies->add(new \yii\web\Cookie([
            'name' => 'site-search-query-string-' . \Yii::$app->user->id,
            'value' => \Yii::$app->request->queryString,
        ]));
        
        $queryParams = \Yii::$app->request->queryParams;
        
        $searchModel = new AdvancedFlatSearch();
        $dataProvider = $searchModel->search($queryParams, 'list-page', true);

        //echo '<pre>'; var_dump($queryParams); echo '</pre>'; die(); 

        $newsDataProvider = new ActiveDataProvider([
            'query' => News::find()->limit(3),
            'pagination' => false,
            'sort' => [
                'attributes' => ['created_at'],
		        'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]);
        
        $cities = !is_null($searchModel->region_id) ? City::find()->forRegion($searchModel->region_id)->asArray()->all() : [];
        if(!empty($cities)) {
            $cities = ArrayHelper::map($cities, 'id', 'name');
        }
        $districts = !is_null($searchModel->city_id) ? District::find()->forCity($searchModel->city_id)->asArray()->all() : [];
        if(!empty($districts)) {
            $districts = ArrayHelper::map($districts, 'id', 'name');
        }
        
        return $this->render('search', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'newsDataProvider' => $newsDataProvider,
            'itemsCount' => $searchModel->itemsCount,
            'regions' => Region::getAllAsList(),
            'cities' => $cities,
            'districts' => $districts,
            'developers' => Developer::getAllAsList(),
            'newbuildingComplexes' => $searchModel->newbuildingComplexes,
            'positionArray' => $searchModel->positionArray,
            'materials' => Newbuilding::getAllMaterialsAsList(),
            'deadlineYears' => Newbuilding::getAllDeadlineYears()
        ]);
    }
    
    /**
     * Flat search on maps
     * 
     * @return mixed
     */
    public function actionMap()
    {
        $this->layout = 'map';
        
        \Yii::$app->response->cookies->add(new \yii\web\Cookie([
            'name' => 'map-search-query-string-' . \Yii::$app->user->id,
            'value' => \Yii::$app->request->queryString,
        ]));
        
        $searchModel = new MapFlatSearch();
        $result = $searchModel->search(\Yii::$app->request->get(), 'page', true);

        if(!is_null($result)) {
            foreach($result as &$item) {
                $item['html'] = $this->renderPartial('/common/_map-flat-list', [
                    'flats' => $item['flats']
                ]);
                unset($item['flats']);
            }
        }

        if (\Yii::$app->request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;            
            \Yii::$app->response->data = [
                'result' => $result,
            ];
            
            return;
        }

        $cities = !is_null($searchModel->region_id) ? City::find()->forRegion($searchModel->region_id)->asArray()->all() : [];
        if(!empty($cities)) {
            $cities = ArrayHelper::map($cities, 'id', 'name');
        }

        $districts = !is_null($searchModel->city_id) ? District::find()->forCity($searchModel->city_id)->asArray()->all() : [];
        if(!empty($districts)) {
            $districts = ArrayHelper::map($districts, 'id', 'name');
        }

        
        $queryParams = \Yii::$app->request->queryParams;
        $selectedCityId = isset($queryParams['city']) && !empty($queryParams['city']) ? $queryParams['city'] : NULL;
        if(is_null($selectedCityId)) {
            $selectedCityId = (\Yii::$app->request->cookies->has('selected-city-' . \Yii::$app->user->id)) ? 
                                    \Yii::$app->request->cookies->get('selected-city-' . \Yii::$app->user->id) : 1;
        }
        $selectedCity = City::findOne($selectedCityId);

        return $this->render('map',  [
            'searchModel' => $searchModel,
            'developers' => Developer::getAllAsList(),
            'newbuildingComplexes' => $searchModel->newbuildingComplexes,
            'positionArray' => $searchModel->positionArray,
            'materials' => Newbuilding::getAllMaterialsAsList(),
            'cities' => $cities,
            'districts' => $districts,
            'regions' => Region::getAllAsList(),
            'deadlineYears' => Newbuilding::getAllDeadlineYears(),
            'selectedCity' => $selectedCity,
        ]);
    }
}
