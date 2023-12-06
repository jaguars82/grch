<?php

namespace app\controllers;

use app\components\exceptions\AppException;
use app\components\traits\CustomRedirects;
use app\models\Bank;
use app\models\Developer;
use app\models\Newbuilding;
use app\models\Document;
use app\models\form\NewbuildingComplexForm;
use app\models\form\ProjectDeclarationForm;
use app\models\search\NewbuildingComplexSearch;
use app\models\search\NewbuildingComplexFlatSearch;
use app\models\search\AdvancedFlatSearch;
use app\models\service\NewbuildingComplex;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use tebe\inertia\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * NewbuildingComplexController implements the CRUD actions for NewbuildingComplex model.
 */
class NewbuildingComplexController extends Controller
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
                    'get-for-developer' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'get-for-developer', 'download-document'],
                        'roles' => ['@'],
                    ],
                ]
            ],
        ];
    }
    
    /**
     * Lists all NewbuildingComplex models.
     * 
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new NewbuildingComplexSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        /*return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'itemsCount' => $searchModel->itemsCount,
            'developers' => Developer::getAllAsList(),
        ]);*/

        return $this->inertia('NewbuildingComplex/Index', [
            'complexes' => ArrayHelper::toArray($dataProvider->getModels()),
            'pagination' => [
                'page' => $dataProvider->getPagination()->getPage(),
                'totalPages' => $dataProvider->getPagination()->getPageCount()
            ]
        ]);
    }

    /**
     * Displays a single NewbuildingComplex model.
     * 
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id, true);       
        
        $newsDataProvider = new ActiveDataProvider([
            'query' => $model->getNews()->limit(4),
            'pagination' => false,
            'sort' => ['attributes' => ['created_at'], 'defaultOrder' => ['created_at' => SORT_DESC]],
        ]);
        
        $contactDataProvider = new ActiveDataProvider([
            'query' => $model->getContacts(),
            'pagination' => false,
            'sort' => ['attributes' => ['id'], 'defaultOrder' => ['id' => SORT_DESC]],
        ]);
        
        $newbuildingComplexesDataProvider = new ActiveDataProvider([
            'query' => NewbuildingComplex::find()
                ->onlyActive()
                ->onlyWithActiveBuildings()
                ->andWhere(['!=', 'id', $model->id])
                ->andWhere(['=', 'developer_id', $model->developer_id]),
                'pagination' => false,
                'sort' => ['attributes' => ['id'], 'defaultOrder' => ['id' => SORT_DESC]],
            ]);

        /*return $this->render('view', [
            'model' => $model,
            'projectDeclaration' => new ProjectDeclarationForm(),
            'newsDataProvider' => $newsDataProvider,
            'newbuildingComplexesDataProvider' => $newbuildingComplexesDataProvider,
            'contactDataProvider' => $contactDataProvider,
        ]);*/

        //echo '<pre>'; var_dump($model);  echo '</pre>'; die;

        $complex = ArrayHelper::toArray($model, [
            'app\models\service\NewbuildingComplex' => [
                'id', 'developer_id', 'name', 'longitude', 'longitude', 'logo', 'detail',
                'newbuildings' => function ($nbc) {
                    return ArrayHelper::toArray($nbc->newbuildings, [
                        'app\models\Newbuilding' => [
                            'id', 'newbuilding_complex_id', 'azimuth', 'name', 'address', 'longitude', 'latitude', 'detail', 'total_floor', 'material', 'status', 'deadline', 'active',
                            'entrances' => function ($newbuilding) {
                                return ArrayHelper::toArray($newbuilding->entrances, [
                                    'app\models\Entrance' => [
                                        'id', 'newbuilding_id', 'name', 'number', 'floors', 'material', 'status', 'deadline', 'azimuth', 'longitude', 'latitude',
                                        'flats' => function ($entrance) {
                                            $flats = ArrayHelper::toArray($entrance->flats, [
                                                'app\models\Flat' => [
                                                    'id', 'newbuilding_id', 'entrance_id', 'address', 'detail', 'area', 'rooms', 'floor', 'index_on_floor', 'price_cash', 'status', 'sold_by_application', 'is_applicated', 'is_reserved', 'created_at', 'updated_at', 'unit_price_cash', 'discount_type', 'discount', 'discount_amount', 'discount_price', 'azimuth', 'notification', 'extra_data', 'composite_flat_id', 'section', 'number', 'layout', 'unit_price_credit', 'price_credit', 'floor_position', 'floor_layout', 'layout_coords', 'is_euro', 'is_studio',
                                                    'has_discount' => function ($flat) {
                                                        return $flat->hasDiscount();
                                                    },
                                                    'price_range' => function ($flat) {
                                                        return $flat->hasDiscount() ? \Yii::$app->formatter->asCurrencyRange(round($flat->allCashPricesWithDiscount[0]['price']), $flat->price_cash) : '';
                                                    }
                                                ]
                                            ]);
                                            return ArrayHelper::map($flats, 'id', function($item) { return $item; }, 'floor');
                                        },
                                    ]
                                ]);
                            }
                        ]
                    ]); 
                },
                'images' => function ($nbc) { return ArrayHelper::toArray($nbc->images); },
                'flats_by_room' => function ($nbc) {
                    $result = [];
                    // add values for apartments with a sertain amount of rooms
                    $roomsAmount = [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5];
                    foreach ($roomsAmount as $roomind => $roomval) {
                        $roomItem = $roomind.'Room';
                        $$roomItem = false;
                        if (!is_null($nbc->getMinFlatPriceForRooms($roomval)) && !is_null($nbc->getMaxFlatPriceForRooms($roomval))) {
                            $$roomItem = [
                                /*'search_url' => Url::to([
                                    'site/search', 
                                    'AdvancedFlatSearch[roomsCount][0]' => $roomval,
                                    'AdvancedFlatSearch[flatType]' => AdvancedFlatSearch::FLAT_TYPE_STANDARD,
                                    'AdvancedFlatSearch[newbuilding_complex][]' => $nbc->id,
                                    'AdvancedFlatSearch[developer][]' => $nbc->developer->id,
                                ]),*/
                                'search_url' => '/site/search?AdvancedFlatSearch[roomsCount][0]='.$roomval.'&AdvancedFlatSearch[flatType]='.AdvancedFlatSearch::FLAT_TYPE_STANDARD.'&AdvancedFlatSearch[newbuilding_complex][]='.$nbc->id.'&AdvancedFlatSearch[developer][]='.$nbc->developer->id,
                                'label' => $roomval.' - комнатные',
                                'price' => \Yii::$app->formatter->asCurrencyRange(round($nbc->getMinFlatPriceForRooms($roomval)), round($nbc->getMaxFlatPriceForRooms($roomval)), 'руб.')
                            ];
                        }
                        array_push($result, $$roomItem);
                    }
                    // add a value for studios
                    $studioItem = false;
                    if (!is_null($nbc->minStudioFlatPrice) && !is_null($nbc->maxStudioFlatPrice)) {
                        $studioItem = [
                            /*'search_url' => Url::to([
                                'site/search', 
                                'AdvancedFlatSearch[flatType]' => AdvancedFlatSearch::FLAT_TYPE_STUDIO,
                                'AdvancedFlatSearch[newbuilding_complex][]' => $nbc->id,
                                'AdvancedFlatSearch[developer][]' =>$nbc->developer->id,
                            ]),*/
                            'search_url' => '/site/search?AdvancedFlatSearch[flatType]='.AdvancedFlatSearch::FLAT_TYPE_STUDIO.'&AdvancedFlatSearch[newbuilding_complex][]='.$nbc->id.'&AdvancedFlatSearch[developer][]='.$nbc->developer->id, 
                            'label' => 'студии',
                            'price' => \Yii::$app->formatter->asCurrencyRange(round($nbc->getMinFlatPriceForRooms($nbc->minStudioFlatPrice)), round($nbc->getMaxFlatPriceForRooms($nbc->maxStudioFlatPrice)), 'руб.')
                        ];
                    }
                    array_push($result, $studioItem);

                    return $result;
                },
            ]
        ]);

        return $this->inertia('NewbuildingComplex/View', [
            'complex' => $complex,
            'otherNC' => ArrayHelper::toArray($newbuildingComplexesDataProvider->getModels()),
        ]);
    }

    /**
     * Finds the NewbuildingComplex model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * 
     * @param integer $id
     * @return NewbuildingComplex the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $withRelatedModels = false)
    {
        $query = NewbuildingComplex::find()
            ->with(['newbuildings', 'images'])
            ->where(['id' => $id]);
        
        if ($withRelatedModels) {
            $query->with(['furnishes.furnishImages']);
        }
        
        if (($model = $query->one()) === null || $model->active == false) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }

        return $model;
    }

    /**
     * Getting newbuilding complexes for given developer.
     * 
     * @param integer $id Developer's ID
     * @return mixed
     */
    public function actionGetForDeveloper($id)
    {
        $idies = explode(',', $id);
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;        
        \Yii::$app->response->data = NewbuildingComplex::find()
            ->forDeveloper($idies)
            ->onlyActive(true)
            ->onlyWithActiveBuildings()
            ->select(['id', 'name'])
            ->orderBy(['id' => SORT_DESC])
            ->asArray()
            ->all();
    }

    /**
     * Getting file for given Newbuilding Complex.
     * 
     * @param integer $id Newbuilding Complexes ID
     * @param integer $file Newbuilding Complexes file origin name
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDownloadDocument($id)
    {
        if (($document = Document::findOne($id)) == null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }

        $fileName = \Yii::getAlias("@webroot/uploads/{$document->file}");
        return \Yii::$app->response->sendFile($fileName, "{$document->name}." . pathinfo($fileName, PATHINFO_EXTENSION));
    }
}
