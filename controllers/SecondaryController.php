<?php

namespace app\controllers;

use app\models\District;
use app\models\SecondaryAdvertisement;
use app\models\SecondaryRoom;
use app\models\SecondaryRoomFee;
use app\models\SecondaryCategory;
use app\models\StatusLabelType;
use app\components\traits\CustomRedirects;
use app\components\SharedDataFilter;
use app\components\VisitBehavior;
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
            'visit' => VisitBehavior::class,
        ];
    }

    public function actionIndex()
    {
        $query = SecondaryAdvertisement::find()->where(['is_active' => 1])->join('INNER JOIN', 'secondary_room as room', 'secondary_advertisement.id = room.advertisement_id');

        // filter data (if we get filter fields' params) before getting ranges' edge points (min, max) from database
        if ($filter = \Yii::$app->request->get('filter')) {
            if (!empty($filter['statusLabel'])) {
                $query->joinWith('statusLabels')->where(['status_label.label_type_id' => $filter['statusLabel']]);
            }
            if (!empty($filter['deal_type'])) $query->andWhere(['deal_type' => (int)$filter['deal_type']]);
            if (!empty($filter['agency'])) $query->andWhere(['agency_id' => $filter['agency']]);
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
            if (!empty($filter['windowviewStreet'])) $query->andWhere(['room.windowview_street' => 1]);
            if (!empty($filter['windowviewYard'])) $query->andWhere(['room.windowview_yard' => 1]);
            if (!empty($filter['panoramicWindows'])) $query->andWhere(['room.panoramic_windows' => 1]);
            if (!empty($filter['concierge'])) $query->andWhere(['room.concierge' => 1]);
            if (!empty($filter['rubbishChute'])) $query->andWhere(['room.rubbish_chute' => 1]);
            if (!empty($filter['gasPipe'])) $query->andWhere(['room.gas_pipe' => 1]);
            if (!empty($filter['closedTerritory'])) $query->andWhere(['room.closed_territory' => 1]);
            if (!empty($filter['playground'])) $query->andWhere(['room.playground' => 1]);
            if (!empty($filter['undergroundParking'])) $query->andWhere(['room.underground_parking' => 1]);
            if (!empty($filter['groundParking'])) $query->andWhere(['room.ground_parking' => 1]);
            if (!empty($filter['openParking'])) $query->andWhere(['room.open_parking' => 1]);
            if (!empty($filter['multilevelParking'])) $query->andWhere(['room.multilevel_parking' => 1]);
            if (!empty($filter['barrier'])) $query->andWhere(['room.barrier' => 1]);
        }

        // get max and min values from database
        $maxPrice = $query->max('price');
        $minPrice = $query->min('price');
        
        $maxArea = $query->max('area');
        $minArea = $query->min('area');
        
        $maxFloor = $query->max('floor');
        $minFloor = $query->min('floor');
        
        $maxTotalFloors = $query->max('total_floors');
        $minTotalFloors = $query->min('total_floors');
        
        $maxKitchenArea = $query->max('kitchen_area');
        $minKitchenArea = $query->min('kitchen_area');
        
        $maxLivingArea = $query->max('living_area');
        $minLivingArea = $query->min('living_area');
        
        $maxBalconyAmount = $query->max('balcony_amount');
        $minBalconyAmount = $query->min('balcony_amount');
        
        $maxLoggiaAmount = $query->max('loggia_amount');
        $minLoggiaAmount = $query->min('loggia_amount');
        
        $maxBuiltYear = $query->max('built_year');
        $minBuiltYear = $query->min('built_year');
        
        // filter data by ranges after getting ranges' edge points (min, max)
        if ($filter = \Yii::$app->request->get('filter')) {
            if (!empty($filter['priceFrom'])) $query->andWhere(['>=', 'room.price', (float)$filter['priceFrom']]);
            if (!empty($filter['priceTo'])) $query->andWhere(['<=', 'room.price', (float)$filter['priceTo']]);
            if (!empty($filter['areaFrom'])) $query->andWhere(['>=', 'room.area', (float)$filter['areaFrom']]);
            if (!empty($filter['areaTo'])) $query->andWhere(['<=', 'room.area', (float)$filter['areaTo']]);
            if (!empty($filter['floorFrom'])) $query->andWhere(['>=', 'room.floor', (int)$filter['floorFrom']]);
            if (!empty($filter['floorTo'])) $query->andWhere(['<=', 'room.floor', (int)$filter['floorTo']]);
            if (!empty($filter['totalFloorsFrom'])) $query->andWhere(['>=', 'room.total_floors', (int)$filter['totalFloorsFrom']]);
            if (!empty($filter['totalFloorsTo'])) $query->andWhere(['<=', 'room.total_floors', (int)$filter['totalFloorsTo']]);
            if (!empty($filter['kitchenAreaFrom'])) $query->andWhere(['>=', 'room.kitchen_area', (float)$filter['kitchenAreaFrom']]);
            if (!empty($filter['kitchenAreaTo'])) $query->andWhere(['<=', 'room.kitchen_area', (float)$filter['kitchenAreaTo']]);
            if (!empty($filter['livingAreaFrom'])) $query->andWhere(['>=', 'room.living_area', (float)$filter['livingAreaFrom']]);
            if (!empty($filter['livingAreaTo'])) $query->andWhere(['<=', 'room.living_area', (float)$filter['livingAreaTo']]);
            if (!empty($filter['balconyFrom'])) $query->andWhere(['>=', 'room.balcony_amount', (int)$filter['balconyFrom']]);
            if (!empty($filter['balconyTo'])) $query->andWhere(['<=', 'room.balcony_amount', (int)$filter['balconyTo']]);
            if (!empty($filter['loggiaFrom'])) $query->andWhere(['>=', 'room.loggia_amount', (int)$filter['loggiaFrom']]);
            if (!empty($filter['loggiaTo'])) $query->andWhere(['<=', 'room.loggia_amount', (int)$filter['loggiaTo']]);
            if (!empty($filter['builtYearFrom'])) $query->andWhere(['>=', 'room.built_year', (int)$filter['builtYearFrom']]);
            if (!empty($filter['builtYearTo'])) $query->andWhere(['<=', 'room.built_year', (int)$filter['builtYearTo']]);
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
            //->orderBy(['last_update_date' => SORT_DESC])
            ->all();

        $advertisementsArr = array();

        foreach ($advertisements as $advertisement) {
            $advRow = ArrayHelper::toArray($advertisement);
            $itemStatusLabels = [];
            foreach ($advertisement->statusLabels as $statusLabel) {
                $labelItem = ArrayHelper::toArray($statusLabel);
                $labelItem['type'] = ArrayHelper::toArray($statusLabel->labelType);
                array_push($itemStatusLabels, $labelItem);
            }
            $advRow['statusLabels'] = $itemStatusLabels;
            $advRow['secondary_room'] = ArrayHelper::toArray($advertisement->secondaryRooms);

            foreach($advRow['secondary_room'] as &$secondaryRoom) {
                $secondaryRoom['agentFee'] = ArrayHelper::toArray(
                    SecondaryRoomFee::find()
                        ->where(['secondary_room_id' => $secondaryRoom['id']])
                        ->all()
                );
            }
            unset($secondaryRoom);

            $advRow['author_DB'] = !empty($advertisement->author_id) ? ArrayHelper::toArray($advertisement->author) : '';
            
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
            'flash' => \Yii::$app->session->getAllFlashes(),
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
                'floor' => [
                    'min' => $minFloor,
                    'max' => $maxFloor
                ],
                'total_floors' => [
                    'min' => $minTotalFloors,
                    'max' => $maxTotalFloors
                ],
                'kitchen_area' => [
                    'min' => $minKitchenArea,
                    'max' => $maxKitchenArea
                ],
                'living_area' => [
                    'min' => $minLivingArea,
                    'max' => $maxLivingArea
                ],
                'balcony_amount' => [
                    'min' => $minBalconyAmount,
                    'max' => $maxBalconyAmount
                ],
                'loggia_amount' => [
                    'min' => $minLoggiaAmount,
                    'max' => $maxLoggiaAmount
                ],
                'built_year' => [
                    'min' => $minBuiltYear,
                    'max' => $maxBuiltYear
                ],
            ],
            'pagination' => [
                'count' => $pagination->totalCount,
                'totalPages' => ceil($pagination->totalCount / $pagination->pageSize),
                'currPage' => isset($pagination->page) ? $pagination->page + 1 : 1,
            ],
            'secondaryCategories' => SecondaryCategory::getCategoryTree(),
            'districts' => District::find()->orderBy(['id' => SORT_ASC])->asArray()->all(),
            'agencies' => SecondaryAdvertisement::getAgenciesWithAdvertisements(),
            'streetList' => SecondaryRoom::getStreetList(),
            'statusLabelTypes' => StatusLabelType::getAllAsList(),
            'filterParams' => isset($filter) ? $filter : []
        ]);
    }

    public function actionView($id)
    {
        $advertisement = SecondaryAdvertisement::findOne($id);
    
        // Convert the advertisement object to an array
        $advertisementArr = ArrayHelper::toArray($advertisement);
    
        // Convert related data to arrays
        $advertisementArr['agency'] = ArrayHelper::toArray($advertisement->agency);
        $advertisementArr['secondary_room'] = ArrayHelper::toArray($advertisement->secondaryRooms);

        // Status labels
        $advStatusLabels = [];
        foreach ($advertisement->statusLabels as $statusLabel) {
            $labelItem = ArrayHelper::toArray($statusLabel);
            $labelItem['type'] = ArrayHelper::toArray($statusLabel->labelType);
            array_push($advStatusLabels, $labelItem);
        }
        $advertisementArr['statusLabels'] = $advStatusLabels;

        foreach($advertisementArr['secondary_room'] as &$secondaryRoom) {
            $secondaryRoom['agentFee'] = ArrayHelper::toArray(
                SecondaryRoomFee::find()
                    ->where(['secondary_room_id' => $secondaryRoom['id']])
                    ->all()
            );
        }
        unset($secondaryRoom);

        $advertisementArr['author_DB'] = !empty($advertisement->author_id) ? ArrayHelper::toArray($advertisement->author) : '';
    
        // Decode 'detail' field in all the objects in the advertisement
        foreach ($advertisementArr['secondary_room'] as &$room) {
            if (isset($room['detail'])) {
                $room['detail'] = str_replace('&nbsp;', ' ', html_entity_decode($room['detail'], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            }
        }
        unset($room); // break the link to avoid possimble mistakes
    
        /**
         * Add info about some parameters of the secondary room from DB
         */
        foreach ($advertisement->secondaryRooms as $key => $room) {
            $params = [
                'category' => 'secondaryCategory',
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
                if (!empty($room->{$param . '_id'}) && isset($room->{$className})) {
                    $advertisementArr['secondary_room'][$key][$param . '_DB'] = ArrayHelper::toArray($room->{$className});
                }
            }
    
            $advertisementArr['secondary_room'][$key]['images'] = ArrayHelper::toArray($room->images);
        }
    
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