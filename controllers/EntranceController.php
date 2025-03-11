<?php

namespace app\controllers;

use app\components\exceptions\AppException;
use app\components\traits\CustomRedirects;
use app\models\Entrance;
use app\models\Flat;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\components\SharedDataFilter;

class EntranceController extends Controller
{
    use CustomRedirects;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['get-for-newbuilding', 'get-flats-by-entrance', 'get-chess-flats-by-entrance', 'get-risers-by-entrances'],
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
     * Getting entrances for given newbuilding.
     * 
     * @param integer $id newbuilding's ID
     * @return mixed
     */
    public function actionGetForNewbuilding($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;        
        $entrances = Entrance::find()
            ->forNewbuilding($id)
            //->select(['id', 'name'])
            //->asArray()
            ->all();
        
        foreach ($entrances as &$entrance) {
            //$entrance['name'] = \Yii::$app->formatter->asCapitalize($entrance['name']);
            $entrance->name = \Yii::$app->formatter->asCapitalize($entrance->name);
            $entrance->name = $entrance->name.', ('.$entrance->newbuilding->name.')';
        }
        
        \Yii::$app->response->data = $entrances;
    }

    
    /**
     * Getting flats for given entrance.
     * 
     * @param integer $id entrance's ID
     * @param boolean $active
     * @return mixed
     */
    public function actionGetFlatsByEntrance($id, $active = true)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON; 
        if ($active === true) {
            $flats = Flat::find()
                ->select(['id', 'number', 'floor'])
                ->where(['entrance_id' => $id])
                ->andWhere(['status' => 0])
                ->orderBy(['number' => SORT_ASC])
                ->all();            
        } else {
            $flats = Flat::find()
                ->select(['id', 'number', 'floor'])
                ->where(['entrance_id' => $id])
                ->orderBy(['number' => SORT_ASC])
                ->all();   
        }

        
        \Yii::$app->response->data = $flats;
    }

        
    /**
     * Getting flats for chess-grid of an entrance.
     * 
     * @param integer $id entrance's ID
     * @return mixed
     */
    public function actionGetChessFlatsByEntrance($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    
        $flats = Flat::find()
            ->where(['entrance_id' => $id])
            ->orderBy(['number' => SORT_ASC])
            ->all();
    
        // Convert flats to array with necessary fields
        $flatsArray = ArrayHelper::toArray($flats, [
            'app\models\Flat' => [
                'id', 'newbuilding_id', 'entrance_id', 'address', 'detail', 'area', 'rooms', 'floor', 'index_on_floor', 'price_cash', 'status', 'sold_by_application', 'is_applicated', 'is_reserved', 'created_at', 'updated_at', 'unit_price_cash', 'discount_type', 'discount', 'discount_amount', 'discount_price', 'azimuth', 'notification', 'composite_flat_id', 'section', 'number', 'number_string', 'layout', 'unit_price_credit', 'price_credit', 'floor_position', 'floor_layout', 'layout_coords', 'is_euro', 'is_studio', 'is_commercial',
                'has_discount' => function ($flat) {
                    return $flat->hasDiscount();
                },
                'price_range' => function ($flat) {
                    return $flat->hasDiscount() ? \Yii::$app->formatter->asCurrencyRange(round($flat->allCashPricesWithDiscount[0]['price']), $flat->price_cash) : '';
                }
            ]
        ]);
    
        // Fetch flats grouped by floor
        $flatsFetchedByFloor = ArrayHelper::map($flatsArray, 'id', function ($item) {
            return $item;
        }, 'floor');
    
        // Process flats on each floor according to its index
        foreach ($flatsFetchedByFloor as $floor => $flatsOnFloor) {
            $floorIsIndexed = true;
            $listOfIndexes = [];
    
            // Check if all flats on the floor have index_on_floor
            foreach ($flatsOnFloor as $flat) {
                if (empty($flat['index_on_floor'])) {
                    $floorIsIndexed = false;
                } else {
                    $listOfIndexes[] = $flat['index_on_floor'];
                }
            }
    
            // Check if the number of indexes matches the number of flats
            if (count($listOfIndexes) !== count($flatsOnFloor)) {
                $floorIsIndexed = false;
            }
    
            // Ensure all indexes are unique
            if (count($listOfIndexes) !== count(array_unique($listOfIndexes))) {
                $floorIsIndexed = false;
            }
    
            // Fill missing indexes with empty cells (gaps)
            if (count($listOfIndexes) > 0) {
                $maxIndex = max($listOfIndexes);
                for ($i = 1; $i <= $maxIndex; $i++) {
                    if (!in_array($i, $listOfIndexes)) {
                        $listOfIndexes[] = $i;
                    }
                }
            }
    
            // If the floor is properly indexed, map flats by their index
            if ($floorIsIndexed) {
                sort($listOfIndexes);
    
                $flatsOnFloorByIndexes = ArrayHelper::map($flatsOnFloor, 'index_on_floor', function ($item) {
                    return $item;
                });
    
                $flatsFetchedByIndex = [];
                foreach ($listOfIndexes as $index) {
                    $flatsFetchedByIndex[$index] = array_key_exists($index, $flatsOnFloorByIndexes) ? $flatsOnFloorByIndexes[$index] : 'filler';
                }
    
                $flatsFetchedByFloor[$floor] = $flatsFetchedByIndex;
            }
        }
    
        \Yii::$app->response->data = $flatsFetchedByFloor;
    }
    

    
    /**
     * Get array with index_on_floor range (for particular entrances or for entyre table)
     */
    public static function actionGetRisersByEntrances($entranceId)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $maxIndex = Flat::find()
            ->where(['entrance_id' => $entranceId])
            ->max('index_on_floor');

        //$maxIndex = 5;

        $rises = array();

        for ($i = 1; $i <= $maxIndex; $i++) {
            array_push($rises, $i);
        }

        \Yii::$app->response->data = $rises;
    }

}