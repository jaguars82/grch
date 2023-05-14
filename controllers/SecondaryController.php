<?php

namespace app\controllers;

use app\models\District;
use app\models\SecondaryAdvertisement;
use app\models\SecondaryRoom;
use app\models\SecondaryCategory;
use app\components\traits\CustomRedirects;
use app\components\SharedDataFilter;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use tebe\inertia\web\Controller;
use yii\helpers\ArrayHelper;
use yii\data\Pagination;

class SecondaryController extends Controller
{
    use CustomRedirects;

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['GET', 'POST'],
                    'view' => ['GET', 'POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'roles' => ['admin', 'manager', 'agent'],
                    ],
                ]
            ],
            [
                'class' => SharedDataFilter::class
            ],
        ];
    }

    public function actionIndex()
    {
        $query = SecondaryAdvertisement::find()->where(['is_active' => 1])->join('INNER JOIN', 'secondary_room as room', 'secondary_advertisement.id = room.advertisement_id');

        // filter data (if we get filter fields' params) before getting ranges' edge points (min, max) from database
        if ($filter = \Yii::$app->request->get('filter')) {
            // echo '<pre>'; var_dump(\Yii::$app->request->get('filter')); echo '</pre>'; die;
            if (!empty($filter['deal_type'])) $query->andWhere(['deal_type' => (int)$filter['deal_type']]);
            if (!empty($filter['category'])) $query->andWhere(['room.category_id' => (int)$filter['category']]);
            if (isset($filter['rooms']) && count($filter['rooms']) > 0) {
                $roomAmounts = $filter['rooms'];
                if (in_array('5+', $filter['rooms'])) {
                    unset($roomAmounts[array_search('5+', $roomAmounts)]);
                    $query->andWhere(['room.rooms' => $roomAmounts])
                    ->orWhere(['>', 'room.rooms', 5]);
                } else {
                    $query->andWhere(['room.rooms' => $roomAmounts]);
                }
            }
            if (!empty($filter['district']) && count($filter['district']) > 0) {
                $query->andWhere(['room.district_id' => $filter['district']]);
            }
            if (is_array($filter['street']) && array_key_exists('value', $filter['street']) && !empty($filter['street']['value']['street_name'])) {
                $query->andWhere(['room.street_name' => $filter['street']['value']['street_name']]);
                if (!empty($filter['street']['value']['street_type_id'])) {
                    $query->andWhere(['room.street_type_id' => $filter['street']['value']['street_type_id']]);
                }
            }
        }
        
        // get max and min values from database
        $maxPrice = $query->max('price');
        $minPrice = $query->min('price');
        
        $maxArea = $query->max('area');
        $minArea = $query->min('area');
        
        // filter data by ranges after getting ranges' edge points (min, max)
        if ($filter = \Yii::$app->request->get('filter')) {
            if (!empty($filter['priceFrom'])) $query->andWhere(['>=', 'room.price', (float)$filter['priceFrom']]);
            if (!empty($filter['priceTo'])) $query->andWhere(['<=', 'room.price', (float)$filter['priceTo']]);
            if (!empty($filter['areaFrom'])) $query->andWhere(['>=', 'room.area', (float)$filter['areaFrom']]);
            if (!empty($filter['areaTo'])) $query->andWhere(['<=', 'room.area', (float)$filter['areaTo']]);
        }

        // get the total number of advertisements
        $count = $query->count();

        // create a pagination object with the total count
        $pagination = new Pagination(['totalCount' => $count]);

        // limit the query using the pagination and retrieve the advertisements
        $advertisements = $query
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy(['creation_date' => SORT_DESC])
            ->all();

        $advertisementsArr = array();

        foreach ($advertisements as $advertisement) {
            $advRow = ArrayHelper::toArray($advertisement);
            $advRow['secondary_room'] = ArrayHelper::toArray($advertisement->secondaryRooms);
            
            /**
             * Add information about room params from data base
             */
            foreach($advertisement->secondaryRooms as $key => $room) {
                $params = [
                    'category' => 'secondaryCategory', // category (e.g. 'flat', 'house' etc.)
                    'property_type' => 'secondaryPropertyType',
                    'building_series' => 'secondaryBuildingSeries',
                    'newbuilding_complex' => 'newbuildingComplex',
                    'newbuilding' => 'newbuilding',
                    'entrance' => 'entrance',
                    'flat' => 'flat',
                    'renovation' => 'secondaryRenovation',
                    'material' => 'buildingMaterial',
                    'region' => 'region',
                    'region_district' => 'regionDistrict',
                    'city' => 'city',
                    'district' => 'district',
                    'street_type' => 'streetType',
                ];

                foreach ($params as $param => $className) {
                    if (!empty($room[$param.'_id'])) {
                        $advRow['secondary_room'][$key][$param.'_DB'] = ArrayHelper::toArray($room[$className]);
                    }
                }

                $advRow['secondary_room'][$key]['images'] = ArrayHelper::toArray($room->images);
            }

            array_push($advertisementsArr, $advRow);
        }

        return $this->inertia('Secondary/Index', [
            'user' => \Yii::$app->user->identity,
            'advertisements' => $advertisementsArr,
            'ranges' => [
                'price' => [
                    'min' => $minPrice,
                    'max' => $maxPrice
                ],
                'area' => [
                    'min' => $minArea,
                    'max' => $maxArea
                ],
            ],
            'pagination' => [
                'count' => $pagination->totalCount,
                'totalPages' => floor($pagination->totalCount / $pagination->pageSize),
                'currPage' => isset($pagination->page) ? $pagination->page + 1 : 1,
            ],
            'secondaryCategories' => SecondaryCategory::getCategoryTree(),
            'districts' => District::find()->orderBy(['id' => SORT_ASC])->asArray()->all(),
            'streetList' => SecondaryRoom::getStreetList(),
            'filterParams' => isset($filter) ? $filter : []
        ]);
    }

    public function actionView($id)
    {
        $advertisement = SecondaryAdvertisement::findOne($id);

        $advertisementArr = ArrayHelper::toArray($advertisement);

        $advertisementArr['secondary_room'] = ArrayHelper::toArray($advertisement->secondaryRooms);
            
        /**
         * Add information about room params from data base
         */
        foreach($advertisement->secondaryRooms as $key => $room) {
            $params = [
                'category' => 'secondaryCategory', // category (e.g. 'flat', 'house' etc.)
                'property_type' => 'secondaryPropertyType',
                'building_series' => 'secondaryBuildingSeries',
                'newbuilding_complex' => 'newbuildingComplex',
                'newbuilding' => 'newbuilding',
                'entrance' => 'entrance',
                'flat' => 'flat',
                'renovation' => 'secondaryRenovation',
                'material' => 'buildingMaterial',
                'region' => 'region',
                'region_district' => 'regionDistrict',
                'city' => 'city',
                'district' => 'district',
                'street_type' => 'streetType',
            ];

            foreach ($params as $param => $className) {
                if (!empty($room[$param.'_id'])) {
                    $advertisementArr['secondary_room'][$key][$param.'_DB'] = ArrayHelper::toArray($room[$className]);
                }
            }

            $advertisementArr['secondary_room'][$key]['images'] = ArrayHelper::toArray($room->images);

        }

        //echo '<pre>'; var_dump($advertisementArr); echo '</pre>'; die;

        return $this->inertia('Secondary/View', [
            'user' => \Yii::$app->user->identity,
            'advertisement' => $advertisementArr,
        ]);
    }

    /*private function addParamsFromDataBase($params, $sourceObject, $targetArray)
    {
        foreach ($params as $param => $className) {
            if (!empty($sourceObject[$param.'_id'])) {
                $targetArray[$param.'_DB'] = ArrayHelper::toArray($sourceObject[$className]);
            }
        }
    }*/

}