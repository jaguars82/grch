<?php

namespace app\controllers;

use app\models\Developer;
use app\models\Agency;
use app\models\Bank;
use app\models\Newbuilding;
use app\models\NewbuildingComplex;
use app\models\News;
use app\models\City;
use app\models\Region;
use app\models\District;
use app\models\Flat;
use app\models\search\AdvancedFlatSearch;
use app\models\search\MapFlatSearch;
use app\models\search\SimpleFlatSearch;
use yii\data\ActiveDataProvider;
use app\components\AuthAmountFilter;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use tebe\inertia\web\Controller;
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
            [
                'class' => AuthAmountFilter::className(),
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

        $newsList = News::find()->onlyActual()->orderBy(['created_at' => SORT_DESC])->all();

        /* $newsDataProvider = new ActiveDataProvider([
            'query' => News::find()->onlyNews()->onlyActual()->limit(4),
            'pagination' => false,
            'sort' => [
                'attributes' => ['created_at'],
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]); */

        /* $actionsDataProvider = new ActiveDataProvider([
            'query' => News::find()->onlyActions()->onlyActual()->limit(2),
            'pagination' => false,
            'sort' => [
                'attributes' => ['created_at'],
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]); */

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
        //return $this->render('index', [
        return $this->inertia('Main/Index', [
            'searchModel' => $searchModel,
            'newsList' => ArrayHelper::toArray($newsList),
            'initialFilterParams' => [
                'maxFlatPrice' => Flat::getMaxFlatPrice(),
                'minFlatPrice' => Flat::getMinFlatPrice(),
                'maxM2Price' => Flat::getMaxFlatPerUnitPrice(),
                'minM2Price' => Flat::getMinFlatPerUnitPrice(),
            ],
            //'newsList' => News::find()->asArray()->all(),
            //'newsDataProvider' =>$newsDataProvider,
            //'actionsDataProvider' => $actionsDataProvider,
            'developerDataProvider' => $developerDataProvider,
            'agencyDataProvider' => $agencyDataProvider,
            'bankDataProvider' => $bankDataProvider,
            'districts' => ArrayHelper::toArray(District::getAllForLocationAsList()),
            'developers' => ArrayHelper::toArray(Developer::getAllAsList()),
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
            'query' => News::find()->onlyActual()->limit(3),
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

       
        //return $this->render('search', [
        return $this->inertia('Main/Search', [
            //'searchModel' => $searchModel,
            'searchModel' => $queryParams,
            'dataProvider' => ArrayHelper::toArray($dataProvider->getModels(), [
                'app\models\Flat' => [
                    'id', 'newbuilding_id', 'entrance_id', 'address', 'detail', 'area', 'rooms', 'floor', 'index_on_floor', 'price_cash', 'status', 'sold_by_application', 'is_applicated', 'is_reserved', 'created_at', 'updated_at', 'unit_price_cash', 'discount_type', 'discount', 'discount_amount', 'discount_price', 'azimuth', 'notification', 'extra_data', 'composite_flat_id', 'section', 'number', 'layout', 'unit_price_credit', 'price_credit', 'floor_position', 'floor_layout', 'layout_coords', 'is_euro', 'is_studio',
                    'newbuilding' => function ($flat) {
                        return ArrayHelper::toArray($flat->newbuilding);
                    },
                    'newbuildingComplex' => function ($flat) {
                        return ArrayHelper::toArray($flat->newbuildingComplex);
                    },
                    'developer' => function ($flat) {
                        return ArrayHelper::toArray($flat->developer);
                    }
                ]
            ]),
            'pagination' => [
                'page' => $dataProvider->getPagination()->getPage(),
                'totalPages' => $dataProvider->getPagination()->getPageCount()
            ],
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
        // $this->layout = 'map';
        
        \Yii::$app->response->cookies->add(new \yii\web\Cookie([
            'name' => 'map-search-query-string-' . \Yii::$app->user->id,
            'value' => \Yii::$app->request->queryString,
        ]));
        
        $searchModel = new MapFlatSearch();
        $result = $searchModel->search(\Yii::$app->request->get(), 'page', true);

        /* if(!is_null($result)) {
            foreach($result as &$item) {
                $item['html'] = $this->renderPartial('/common/_map-flat-list', [
                    'flats' => $item['flats']
                ]);
                unset($item['flats']);
            }
        } */

        /*if (\Yii::$app->request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;            
            \Yii::$app->response->data = [
                'result' => $result,
            ];
            
            return;
        }*/

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

        // return $this->render('map',  [
        return $this->inertia('Main/Map', [
            'searchModel' => $searchModel,
            'complexes' => ArrayHelper::toArray($result), 
            'developers' => Developer::getAllAsList(),
            'newbuildingComplexes' => $searchModel->newbuildingComplexes,
            'positionArray' => ArrayHelper::toArray($searchModel->positionArray),
            'materials' => Newbuilding::getAllMaterialsAsList(),
            'cities' => $cities,
            'districts' => $districts,
            'regions' => Region::getAllAsList(),
            'deadlineYears' => Newbuilding::getAllDeadlineYears(),
            'selectedCity' => ArrayHelper::toArray($selectedCity),
        ]);
    }
}
