<?php

namespace app\controllers;

use app\models\SecondaryAdvertisement;
use app\models\SecondaryRoom;
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
                    'view' => ['GET', 'POST']
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
        $query = SecondaryAdvertisement::find()->where(['is_active' => 1]);

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
            'pagination' => [
                'count' => $pagination->totalCount,
                'totalPages' => floor($pagination->totalCount / $pagination->pageSize),
                'currPage' => isset($pagination->page) ? $pagination->page + 1 : 1,
            ],
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