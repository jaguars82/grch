<?php

namespace app\controllers;

use app\components\traits\CustomRedirects;
use app\components\SharedDataFilter;
use app\models\Newbuilding;
use app\models\NewbuildingComplex;
use app\models\form\FlatForm;
use app\models\service\Flat;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use tebe\inertia\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\FloorLayout;
use yii\helpers\Url;

/**
 * FlatController implements the CRUD actions for Flat model.
 */
class FlatController extends Controller
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
                        'actions' => ['view'],
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
     * Displays a single Flat model.
     * 
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id, true);
        
        $flatDataProvider = new ActiveDataProvider([
            'query' => $model->newbuildingComplex->getFlats()->with(['flatImages', 'developer', 'favorites'])->analogs($model)->limit(3),
            'pagination' => false,
            'sort' => ['attributes' => ['id'], 'defaultOrder' => ['id' => SORT_DESC]],
        ]);
        
        $contactDataProvider = new ActiveDataProvider([
            'query' => $model->newbuildingComplex->getContacts(),
            'pagination' => false,
            'sort' => ['attributes' => ['id'], 'defaultOrder' => ['id' => SORT_DESC]],
        ]);

        $newbuildingComplexDataProvider = new ActiveDataProvider([
            //'query' => NewbuildingComplex::find()->forDeveloper($model->developer->id)->onlyActive()->onlyWithActiveBuildings()->where(['!=', 'id', $model->newbuildingComplex->id])->limit(6),
            'query' => NewbuildingComplex::find()->onlyActive()->onlyWithActiveBuildings()->andWhere(['!=', 'id', $model->newbuildingComplex->id])->andWhere(['=', 'developer_id', $model->developer->id])/*->limit(6)*/,
            'pagination' => false,
            'sort' => ['attributes' => ['id'], 'defaultOrder' => ['id' => SORT_DESC]],
        ]);

        
        return $this->inertia('Flat/View', [
            'flatStatuses' => Flat::$status,
            'flat' => ArrayHelper::toArray($model, [
                'app\models\service\Flat' => [
                    'id', 'newbuilding_id', 'entrance_id', 'address', 'detail', 'area', 'rooms', 'floor', 'index_on_floor', 'price_cash', 'status', 'sold_by_application', 'is_applicated', 'is_reserved', 'created_at', 'updated_at', 'unit_price_cash', 'discount_type', 'discount', 'discount_amount', 'discount_price', 'azimuth', 'notification', /*'extra_data', */'composite_flat_id', 'section', 'number', 'layout', 'unit_price_credit', 'price_credit', 'floor_position', 'floor_layout', 'layout_coords', 'is_euro', 'is_studio', 'is_commercial',
                    'priceChange' => function ($flat) {
                        return $flat->priceChanges;
                    },
                    'isFavorite' => function ($flat) { return $flat->isFavorite() > 0; },
                    'hasDiscount' => function ($flat) { return $flat->hasDiscount(); },
                    'allDiscounts' => function ($flat) { return $flat->allCashPricesWithDiscount; },
                    'priceRange' => function ($flat) { return $flat->hasDiscount() ? \Yii::$app->formatter->asCurrencyRange(round($flat->allCashPricesWithDiscount[0]['price']), $flat->price_cash) : null; },
                    'entranceAzimuth' => function ($flat) { return $flat->entrance->azimuth; },
                    'masterPlan' => function ($flat) { return $flat->newbuildingComplex->master_plan; },
                    'floorBackground' => function ($flat) { return $flat->floorLayout !== null ? $flat->floorLayout->image : null; },
                    'deadLine' => function ($flat) { return strtotime(date("Y-m-d")) > strtotime($flat->newbuilding->deadline) ? 'Позиция сдана' : \Yii::$app->formatter->asQuarterAndYearDate($flat->newbuilding->deadline); },
                    'svgViewBox' => function ($flat) { return $flat->floorLayoutSvgViewBox; },
                    'neighboringFlats' => function ($flat) { return ArrayHelper::toArray($flat->getNeighboringFlats()->all()); },
                    //'floorLayoutImage' => function ($flat) { return $flat->floorLayoutSvg; },
                    'developer' => function ($flat) {
                        return ArrayHelper::toArray($flat->developer, [
                            'app\models\Developer' => [
                                'id', 'name',
                                'hasRepresentative' => function ($developer) {
                                    return $developer->hasRepresentative();
                                }
                            ]
                        ]);
                    },
                    'complex' => function ($flat) {
                        return ArrayHelper::toArray($flat->newbuildingComplex, [
                            'app\models\NewbuildingComplex' => [
                                'id', 'developer_id', 'name', 'longitude', 'latitude', 'logo', 'detail', 'offer_new_price_permit', 'project_declaration', 'algorithm', 'offer_info', 'created_at', 'updated_at', 'active', 'region_id', 'city_id', 'district_id', 'street_type_id', 'street_name', 'building_type_id', 'building_number', 'master_plan',
                                'address' => function ($nbc) { return $nbc->address; },
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
                                'freeFlats' => function ($nbc) {
                                    return \Yii::$app->formatter->asPercent($nbc->freeFlats);
                                },
                                'minYearlyRate' => function ($nbc) {
                                    return !is_null($nbc->minYearlyRate) ? \Yii::$app->formatter->asPercent($nbc->minYearlyRate) : null;
                                },
                                'nearestDeadline' => function ($nbc) {
                                    return !is_null($nbc->nearestDeadline) ? \Yii::$app->formatter->asQuarterAndYearDate($nbc->nearestDeadline) : 'нет данных';
                                },
                                'advantages' => function ($nbc) {
                                    return !is_null($nbc->advantages) ? $nbc->advantages : [];
                                },
                            ]
                        ]);
                    },
                    'building' => function ($flat) {
                        return ArrayHelper::toArray($flat->newbuilding, ['app\models\Newbuilding' => ['id', 'name', 'total_floor', 'material']]);
                    },
                    'entrance' => function ($flat) {
                        return ArrayHelper::toArray($flat->entrance, ['app\models\Entrance' => ['id', 'name', 'number']]);
                    },
                ]
            ]),
            'otherNC' => ArrayHelper::toArray($newbuildingComplexDataProvider->getModels()),
        ]);
    }

    /**
     * Finds the Flat model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * 
     * @param integer $id
     * @return Flat the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $withRelatedModels = false)
    {
        $query = Flat::find()->where(['id' => $id])->with(['newbuildingComplex']);
        
        if ($withRelatedModels) {
            $query->with(['newbuildingComplex.furnishes.furnishImages']);
        }
        
        if (($model = $query->one()) === null || $model->newbuildingComplex->active == false) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }

        return $model;
    }
}
