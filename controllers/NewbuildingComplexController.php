<?php

namespace app\controllers;

use app\components\exceptions\AppException;
use app\components\traits\CustomRedirects;
use app\components\SharedDataFilter;
use app\components\VisitBehavior;
use app\models\Bank;
use app\models\Developer;
use app\models\Newbuilding;
use app\models\Flat;
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
            [
                'class' => SharedDataFilter::class
            ],
            'visit' => VisitBehavior::class,
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

        $complex = ArrayHelper::toArray($model, [
            'app\models\service\NewbuildingComplex' => [
                'id', 'developer_id', 'name', 'latitude', 'longitude', 'logo', 'detail',
                'address' => function ($nbc) { return $nbc->address; },
                'documents' => function ($nbc) { return $nbc->documents; },
                'banks' => function ($nbc) { return $nbc->banks; },
                'newbuildings' => function ($nbc) {
                    return ArrayHelper::toArray($nbc->activeNewbuildings, [
                        'app\models\Newbuilding' => [
                            'id', 'newbuilding_complex_id', 'azimuth', 'name', 'address', 'longitude', 'latitude', 'detail', 'total_floor', 'material', 'status', 'deadline', 'active',
                            'aviableFlats' => function ($newbuilding) { return $newbuilding->getActiveFlats()->count(); },
                            'reservedFlats' => function ($newbuilding) { return $newbuilding->getReservedFlats()->count(); },
                            'deadlineString' => function ($newbuilding) { return (empty($newbuilding->deadline) ? 'Нет данных' : strtotime(date("Y-m-d")) > strtotime($newbuilding->deadline)) ? 'позиция сдана' : \Yii::$app->formatter->asQuarterAndYearDate($newbuilding->deadline); },
                            'totalFloorString' => function ($newbuilding) { return empty($newbuilding->total_floor) ? 'этажность не указана' : $newbuilding->total_floor.' этажей'; },
                            'entrances' => function ($newbuilding) {
                                return ArrayHelper::toArray($newbuilding->entrances, [
                                    'app\models\Entrance' => [
                                        'id', 'newbuilding_id', 'name', 'number', 'floors', 'material', 'status', 'deadline', 'azimuth', 'longitude', 'latitude',
                                        'aviableFlats' => function ($entrance) { return $entrance->getActiveFlats()->count(); },
                                        'reservedFlats' => function ($entrance) { return $entrance->getReservedFlats()->count(); },
                                        'deadlineString' => function ($entrance) { return (is_null($entrance->deadline) ? 'нет данных' : strtotime(date("Y-m-d")) > strtotime($entrance->deadline)) ? 'подъезд сдан' : \Yii::$app->formatter->asQuarterAndYearDate($entrance->deadline); },
                                        'flatStatuses' => function ($entrance) {
                                            $statuses = $entrance->getFlats()
                                                ->select('status')
                                                ->distinct()
                                                ->column();

                                            sort($statuses);
                                            
                                            return $statuses;
                                        },
                                    ]
                                ]);
                            }
                        ]
                    ]); 
                },
                'images' => function ($nbc) { return ArrayHelper::toArray($nbc->images); },
                'materials' => function ($nbc) { return $nbc->materials; },
                'furnishes' => function ($nbc) { 
                    return ArrayHelper::toArray($nbc->furnishes, [
                        'app\models\Furnish' => [
                            'id', 'name', 'detail',
                            'furnishImages' => function ($furnish) {
                                return $furnish->getFurnishImages()->asArray()->all();
                            }
                        ]
                    ]);
                },
                'areaRange' => function ($nbc) {
                   return \Yii::$app->formatter->asAreaRange($nbc->minFlatArea, $nbc->maxFlatArea);
                },
                'priceRange' => function ($nbc) {
                    $prices = ['min' => 0, 'max' => 0];
                    $minPrice = $nbc->minFlatPrice;
                    if ($minPrice > 0) $prices['min'] = round($minPrice);
                    $maxPrice = $nbc->maxFlatPrice;
                    if ($maxPrice > 0) $prices['max'] = round($maxPrice);
                    return \Yii::$app->formatter->asCurrencyRange($prices['min'], $prices['max']);
                },
                'freeFlats' => function ($nbc) {
                    return \Yii::$app->formatter->asPercent($nbc->freeFlats);
                },
                'nearestDeadline' => function ($nbc) {
                    return !is_null($nbc->nearestDeadline) ? \Yii::$app->formatter->asQuarterAndYearDate($nbc->nearestDeadline) : 'нет данных';
                },
                'minYearlyRate' => function ($nbc) {
                    return !is_null($nbc->minYearlyRate) ? \Yii::$app->formatter->asPercent($nbc->minYearlyRate) : null;
                },
                'maxDiscount' => function ($nbc) {
                    return !is_null($nbc->maxDiscount) ? \Yii::$app->formatter->asPercent($nbc->maxDiscount) : null;
                },
                'flats_by_room' => function ($nbc) {
                    $result = [];
                    // add values for apartments with a sertain amount of rooms
                    $roomsAmount = [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5];
                    foreach ($roomsAmount as $roomind => $roomval) {
                        $roomItem = $roomind.'Room';
                        $$roomItem = false;
                        if (!is_null($nbc->getMinFlatPriceForRooms($roomval)) && !is_null($nbc->getMaxFlatPriceForRooms($roomval))) {
                            $$roomItem = [
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
                            'search_url' => '/site/search?AdvancedFlatSearch[flatType]='.AdvancedFlatSearch::FLAT_TYPE_STUDIO.'&AdvancedFlatSearch[newbuilding_complex][]='.$nbc->id.'&AdvancedFlatSearch[developer][]='.$nbc->developer->id, 
                            'label' => 'студии',
                            'price' => \Yii::$app->formatter->asCurrencyRange(round($nbc->getMinFlatPriceForRooms($nbc->minStudioFlatPrice)), round($nbc->getMaxFlatPriceForRooms($nbc->maxStudioFlatPrice)), 'руб.')
                        ];
                    }
                    array_push($result, $studioItem);

                    return $result;
                },
                'advantages' => function ($nbc) {
                    return !is_null($nbc->advantages) ? $nbc->advantages : [];
                },
            ]
        ]);

        return $this->inertia('NewbuildingComplex/View', [
            'flatStatuses' => Flat::$status,
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
    public function actionGetForDeveloper($id, $active = true)
    {
        $idies = explode(',', $id);

        $query = NewbuildingComplex::find()->forDeveloper($idies);

        if (!empty(\Yii::$app->request->post('city_id'))) {
            $query->andWhere(['city_id' => \Yii::$app->request->post('city_id')]);

            if (!empty(\Yii::$app->request->post('district_id'))) {
                $query->andWhere(['district_id' => \Yii::$app->request->post('district_id')]);
            }
        }

        if (!empty(\Yii::$app->request->post('region_id')) && empty(\Yii::$app->request->post('city_id'))) {
            $query->andWhere(['region_id' => \Yii::$app->request->post('region_id')]);
        }

        $query
            ->onlyActive($active)
            ->onlyWithActiveBuildings()
            ->select(['id', 'name'])
            ->orderBy(['name' => SORT_ASC])
            ->asArray();
        
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        \Yii::$app->response->data = $query->all();
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
