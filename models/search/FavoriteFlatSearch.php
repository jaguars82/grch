<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use app\models\Flat;
use app\models\Favorite;

/**
 * FavoriteFlatSearch represents the model behind the search form of `app\models\Flat`.
 */
class FavoriteFlatSearch extends Flat
{
    public $itemsCount;
    
    public $region_id, $city_id, $street_name, $developer, $newbuilding_complex;
    public $priceFrom = NULL, $priceTo = NULL;
    public $areaFrom = NULL, $areaTo = NULL;
    public $floorFrom = NULL, $floorTo = NULL, $totalFloor = NULL;
    public $deadline, $update_date;

    public $roomsCount = [];
    public $district = [];
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['floorFrom', 'floorTo', 'totalFloor', 'developer', 'newbuilding_complex', 'region_id', 'city_id'], 'integer'],
            [['priceFrom', 'priceTo', 'areaFrom', 'areaTo'], 'double'],
            [['street_name', 'deadline', 'update_date'], 'string'],
            [['roomsCount', 'district'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'priceFrom' => 'Стоимость',
            'areaFrom' => 'Площадь',
            'floorFrom' => 'Этаж',
            'totalFloor' => 'Этажей не более',
            'deadline' => 'Сдан',
            'update_date' => 'Обновлен не позже',
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $isActive = true)
    {
        $query = Favorite::find()
            ->forCurrentUser()
            ->join('INNER JOIN', 'flat f1', 'f1.id = favorite.flat_id')
            ->joinWith(['flat.developer d1', 'flat.newbuildingComplex nc1'])
            ->select([
                'flat.*',
                'favorite.*',
                'minCostWithDiscount' => new Expression('IF(f1.price_credit is not null, LEAST(f1.price_cash * (1 + f1.discount), f1.price_credit * (1 + f1.discount)), f1.price_cash * (1 + f1.discount))'),
                'maxCostWithDiscount' => new Expression('IF(f1.price_credit is not null, GREATEST(f1.price_cash * (1 + f1.discount), f1.price_credit * (1 + f1.discount)), f1.price_cash * (1 + f1.discount))')
            ]);
        
        if ($isActive) {
            $query->onlyActive();
            $pageParam = 'active-page';
        } else {
            $query->onlyArchive();
            $pageParam = 'archive-page';
        }
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageParam' => $pageParam,
                'pageSizeParam' => false, 
                'pageSize' => 9,
            ],
            'sort' => [
                'attributes' => [
                    'costWithDiscount' => [
                        'label' => 'Стоимость',
                        'asc' => ['minCostWithDiscount' => SORT_ASC],
                        'desc' => ['maxCostWithDiscount' => SORT_DESC],
                    ],
                    'area' => [
                        'label' => 'Площадь',
                    ],
                    'roomsCount' => [
                        'label' => 'Кол-во комнат',
                    ],
                    'developer' => [
                        'asc' => ['d1.name' => SORT_ASC],
                        'desc' => ['d1.name' => SORT_DESC],
                        'label' => 'Застройщик',
                    ],
                    'newbuilding_complex' => [
                        'asc' => ['nc1.name' => SORT_ASC],
                        'desc' => ['nc1.name' => SORT_DESC],
                        'label' => 'ЖК',
                    ],
                ],
            ],
        ]);
            
        $this->fillAttributes($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['>=', 'f1.area', $this->areaFrom])
            ->andFilterWhere(['<=', 'f1.area', $this->areaTo])
            ->andFilterWhere(['>=', 'f1.floor', $this->floorFrom])
            ->andFilterWhere(['<=', 'f1.floor', $this->floorTo]);
        
        if (isset($this->roomsCount) && !empty($this->roomsCount)) {
            if(in_array(4, $this->roomsCount)) {
                $query->andWhere(['or', 
                    ['in', 'f1.rooms', $this->roomsCount],
                    ['>=', 'f1.rooms', 5]
                ]);
            } else {
                $query->andWhere(['in', 'f1.rooms', $this->roomsCount]);
            }
        }
        
        if (isset($this->priceFrom) && !empty($this->priceFrom)) {
            $query->andWhere("f1.price_cash >= :price_from", [':price_from' => $this->priceFrom]);
        }
        
        if (isset($this->priceTo) && !empty($this->priceTo)) {
            $query->andWhere("f1.price_cash <= :price_to", [':price_to' => $this->priceTo]);
        }
        
        if (isset($this->update_date) && !empty($this->update_date)) {
            $updateDate = \Yii::$app->formatter->asDate($this->update_date, 'php:Y-m-d H:i:s');
            $query->andWhere("f1.updated_at != '0000-00-00 00:00:00'")
                ->andWhere(['>= ', 'f1.updated_at', $updateDate]);
        }
        
        if ((isset($this->totalFloor) && !empty($this->totalFloor))
            | (isset($this->region_id) && !empty($this->region_id))
            | (isset($this->city_id) && !empty($this->city_id))
            | (isset($this->district) && !empty($this->district))
            | (isset($this->street_name) && !empty($this->street_name))
            | (isset($this->newbuilding_complex) && !empty($this->newbuilding_complex))
            | (isset($this->developer) && !empty($this->developer))
            | (isset($this->deadline) && !empty($this->deadline))
        ) {
            $query->join('INNER JOIN', 'newbuilding n1', 'n1.id = f1.newbuilding_id');
                    
            if (isset($this->totalFloor) && !empty($this->totalFloor)) {
                $query->andWhere("n1.total_floor <= :total_floor", [':total_floor' => $this->totalFloor]);
            }
            
            if (isset($this->newbuilding_complex) && !empty($this->newbuilding_complex)) {
                $query->andWhere(['n1.newbuilding_complex_id' => $this->newbuilding_complex]);
            }
            
            if (isset($this->deadline) && !empty($this->deadline)) {
                $deadline = \Yii::$app->formatter->asDate($this->deadline, 'php:Y-m-d H:i:s');
                $query->andWhere(['n1.deadline' => $deadline]);
            }
            
            if ((isset($this->developer) && !empty($this->developer))
                | (isset($this->region_id) && !empty($this->region_id))
                | (isset($this->city_id) && !empty($this->city_id))
                | (isset($this->district) && !empty($this->district))
                | (isset($this->street_name) && !empty($this->street_name))
            ) {
                $query->join('INNER JOIN', 'newbuilding_complex nc2', 'nc2.id = n1.newbuilding_complex_id');
                                                        
                if (isset($this->region_id) && !empty($this->region_id)) {
                    $query->andWhere(['nc2.region_id' => $this->region_id]);
                }

                if (isset($this->city_id) && !empty($this->city_id)) {
                    $query->andWhere(['nc2.city_id' => $this->city_id]);
                }

                if (isset($this->district) && !empty($this->district)) {
                    $query->andWhere(['in', 'nc2.district_id', $this->district]);
                }
                
                if (isset($this->street_name) && !empty($this->street_name)) {
                    $query->andWhere(['like', 'nc2.street_name', $this->street_name]);
                }

                if (isset($this->developer) && !empty($this->developer)) {
                    $query->andWhere(['nc2.developer_id' => $this->developer]);
                }
            }
        }
        
        $this->itemsCount = $dataProvider->getTotalCount();
        
        return $dataProvider;
    }
    
    /**
     * Fill search modelattributes from array
     * 
     * @param array $params
     * @return mixed
     */
    private function fillAttributes($params)
    {
        if (!isset($params['FavoriteFlatSearch'])) {
            return;
        }
        
        if (isset($params['FavoriteFlatSearch']['region_id'])) {
            $this->region_id = $params['FavoriteFlatSearch']['region_id'];
        }
        
        if (isset($params['FavoriteFlatSearch']['city_id'])) {
            $this->city_id = $params['FavoriteFlatSearch']['city_id'];
        }

        if (isset($params['FavoriteFlatSearch']['district'])) {
            $this->district = $params['FavoriteFlatSearch']['district'];
        }

        if (isset($params['FavoriteFlatSearch']['street_name'])) {
            $this->street_name = $params['FavoriteFlatSearch']['street_name'];
        }
        
        if (isset($params['FavoriteFlatSearch']['developer'])) {
            $this->developer = $params['FavoriteFlatSearch']['developer'];
        }
        
        if (isset($params['FavoriteFlatSearch']['newbuilding_complex'])) {
            $this->newbuilding_complex = $params['FavoriteFlatSearch']['newbuilding_complex'];
        }
        
        if (isset($params['FavoriteFlatSearch']['roomsCount'])) {
            $this->roomsCount = $params['FavoriteFlatSearch']['roomsCount'];
        }
        
        if (isset($params['FavoriteFlatSearch']['priceFrom'])) {
            $this->priceFrom = $params['FavoriteFlatSearch']['priceFrom'];
        }
        
        if (isset($params['FavoriteFlatSearch']['priceTo'])) {
            $this->priceTo = $params['FavoriteFlatSearch']['priceTo'];
        }
        
        if (isset($params['FavoriteFlatSearch']['areaFrom'])) {
            $this->areaFrom = $params['FavoriteFlatSearch']['areaFrom'];
        }
        
        if (isset($params['FavoriteFlatSearch']['areaTo'])) {
            $this->areaTo = $params['FavoriteFlatSearch']['areaTo'];
        }
        
        if (isset($params['FavoriteFlatSearch']['floorFrom'])) {
            $this->floorFrom = $params['FavoriteFlatSearch']['floorFrom'];
        }
        
        if (isset($params['FavoriteFlatSearch']['floorTo'])) {
            $this->floorTo = $params['FavoriteFlatSearch']['floorTo'];
        }
        
        if (isset($params['FavoriteFlatSearch']['totalFloor'])) {
            $this->totalFloor = $params['FavoriteFlatSearch']['totalFloor'];
        }
        
        if (isset($params['FavoriteFlatSearch']['deadline'])) {
            $this->deadline = $params['FavoriteFlatSearch']['deadline'];
        }
        
        if (isset($params['FavoriteFlatSearch']['update_date'])) {
            $this->update_date = $params['FavoriteFlatSearch']['update_date'];
        }
    }
}
