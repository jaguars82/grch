<?php

namespace app\models\search;

use app\models\Flat;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\Expression;
use app\components\CityLocation;
use app\models\District;

/**
 * AdvancedFlatSearch represents the model behind the search form of `app\models\Flat`.
 */
class AdvancedFlatSearch extends Flat
{
    const SCENARIO_SIMPLE = 'simple';

    const FLAT_TYPE_STANDARD = 0;
    const FLAT_TYPE_EURO = 1;
    const FLAT_TYPE_STUDIO = 2;

    public static $flat_types = [
        self::FLAT_TYPE_STANDARD => 'Стандарт',
        self::FLAT_TYPE_EURO => 'Евро пл.',
        self::FLAT_TYPE_STUDIO => 'Студия',
    ];

    const PRICE_TYPE_ALL = 0;
    const PRICE_TYPE_PER_AREA = 1;

    public static $price_types = [
        self::PRICE_TYPE_ALL => 'За все',
        self::PRICE_TYPE_PER_AREA => 'За м²'
    ];

    public $itemsCount;

    public $street_name, $region_id, $city_id, $developer, $newbuilding_complex;
    public $newbuilding_array = NULL;
    public $priceFrom = NULL, $priceTo = NULL;
    public $areaFrom = NULL, $areaTo = NULL;
    public $floorFrom = NULL, $floorTo = NULL;
    public $material = NULL, $newbuilding_status, $update_date;
    public $flatType = NULL, $priceType = NULL;
    public $totalFloorFrom = NULL, $totalFloorTo = NULL;
    public $deadlineYear = NULL;

    public $roomsCount = [];
    public $district = [];
    public $newbuildingComplexes = [];
    public $positionArray = [];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['floorFrom', 'floorTo', 'developer', 'newbuilding_complex', 'region_id', 'city_id', 'flatType', 'priceType', 'totalFloorFrom', 'totalFloorTo', 'deadlineYear'], 'integer'],
            [['priceFrom', 'priceTo', 'areaFrom', 'areaTo'], 'double'],
            [['street_name', 'material', 'update_date'], 'string'],
            [['newbuilding_status', 'district', 'roomsCount'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        $commonFields = [
            'roomsCount', 'developer', 'newbuilding_complex', 'district', 'priceFrom',
            'priceTo', 'areaFrom', 'areaTo', 'address', 'flatType', 'priceType',
            'totalFloorFrom', 'totalFloorTo'
        ];
        return [
            self::SCENARIO_DEFAULT => array_merge($commonFields, [
                'floorFrom', 'floorTo', 'region_id', 'city_id', 'street_name', 
                'material', 'update_date', 'newbuilding_status', 'deadlineYear'
            ]),
            self::SCENARIO_SIMPLE => $commonFields,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'newbuilding_array' => 'Позиции',
            'priceFrom' => 'Стоимость',
            'totalFloorFrom' => 'Этажность',
            'areaFrom' => 'Площадь',
            'floorFrom' => 'Этаж',
            'newbuilding_status' => 'Сдан',
            'update_date' => 'Обновлен не позже',
            'is_euro' => 'Европланировка',
            'is_studio' => 'Студия'
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $pageParam = 'page', $isReturnFalseWhenNoData = false)
    {
        $query = Flat::find()
            ->select([
                'flat.*',
                'latitude' => 'n1.latitude',
                'longitude' => 'n1.longitude',
                'minCostWithDiscount' => new Expression('IF(price_credit is not null, LEAST(price_cash * (1 + discount), price_credit * (1 + discount)), price_cash * (1 + discount))'),
                'maxCostWithDiscount' => new Expression('IF(price_credit is not null, GREATEST(price_cash * (1 + discount), price_credit * (1 + discount)), price_cash * (1 + discount))')
            ])
            ->onlyActive()
            ->joinWith(['developer d1', 'newbuildingComplex nc1'])
            ->with(['flatImages', 'favorites']);

        if (!$this->fillAttributes($params, $isReturnFalseWhenNoData) || !$this->validate()) {
            return $this->getResultWhenBadData();
        }

		$query->andWhere(['>', 'price_cash', 0]);

        $this->applyFilters($query);

        $this->getPositionArray();

        return $this->getResult($query, $pageParam);
    }

    /**
     * Apply filter to given search query
     *
     * @param type $query
     */
    protected function applyFilters($query)
    {
        $query->andFilterWhere(['>=', 'area', $this->areaFrom])
            ->andFilterWhere(['<=', 'area', $this->areaTo])
            ->andFilterWhere(['>=', 'floor', $this->floorFrom])
            ->andFilterWhere(['<=', 'floor', $this->floorTo]);

        if (isset($this->roomsCount) && !empty($this->roomsCount)) {
            if(in_array(4, $this->roomsCount)) {
                $query->andWhere(['or', 
                    ['in', 'rooms', $this->roomsCount],
                    ['>=', 'rooms', 5]
                ]);
            } else {
                $query->andWhere(['in', 'rooms', $this->roomsCount]);
            }
        }

        if(isset($this->flatType)) {
            if($this->flatType == self::FLAT_TYPE_STANDARD) {
                $query->andWhere(['and',
                    ['flat.is_euro' => false],
                    ['flat.is_studio' => false]
                ]);
            }
            if($this->flatType == self::FLAT_TYPE_EURO) {
                $query->andWhere(['flat.is_euro' => true]);
            }
            if($this->flatType == self::FLAT_TYPE_STUDIO) {
                $query->andWhere(['flat.is_studio' => true]);
            }
        }
        
        if($this->priceType == self::PRICE_TYPE_ALL) {
            if (isset($this->priceFrom) && !empty($this->priceFrom)) {
                $query->andWhere("price_cash >= :price_from", [':price_from' => $this->priceFrom]);
            }
            if (isset($this->priceTo) && !empty($this->priceTo)) {
                $query->andWhere("price_cash <= :price_to", [':price_to' => $this->priceTo]);
            }
        } else if($this->priceType == self::PRICE_TYPE_PER_AREA) {
            $query->andWhere('price_cash / area >= :price_from', [
                ':price_from' => $this->priceFrom
            ]);
            $query->andWhere('price_cash / area <= :price_to', [
                ':price_to' => $this->priceTo
            ]);
        }

        if (isset($this->update_date) && !empty($this->update_date)) {
            $updateDate = \Yii::$app->formatter->asDate($this->update_date, 'php:Y-m-d H:i:s');
            $query->andWhere("flat.updated_at != '0000-00-00 00:00:00'")
                ->andWhere(['>= ', 'flat.updated_at', $updateDate]);
        }

        if (isset($this->newbuilding_array) && count($this->newbuilding_array)) {
            $query->andWhere(['IN', 'newbuilding_id', $this->newbuilding_array]);
        }

        $query->join('INNER JOIN', 'newbuilding as n1', 'n1.id = flat.newbuilding_id');
        $query->join('INNER JOIN', 'newbuilding_complex as nc2', 'nc2.id = n1.newbuilding_complex_id');

        if (isset($this->totalFloorFrom) && !empty($this->totalFloorFrom)) {
            $query->andWhere("n1.total_floor >= :total_floor", [':total_floor' => $this->totalFloorFrom]);
        }

        if (isset($this->totalFloorTo) && !empty($this->totalFloorTo)) {
            $query->andWhere("n1.total_floor <= :total_floor", [':total_floor' => $this->totalFloorTo]);
        }

        if (isset($this->newbuilding_complex) && !empty($this->newbuilding_complex)) {
            $query->andWhere(['n1.newbuilding_complex_id' => $this->newbuilding_complex]);
        }

        if (isset($this->material) && !empty($this->material)) {
            $query->andWhere(['n1.material' => $this->material]);
        }

        if (isset($this->newbuilding_status) && !empty($this->newbuilding_status)) {
            $query->andWhere(['n1.status' => $this->newbuilding_status]);
        }

        if (isset($this->deadlineYear) && !empty($this->deadlineYear)) {
            $query->andWhere(
                'EXTRACT(YEAR FROM n1.deadline) = :deadlineYear',
                [
                    ':deadlineYear' => $this->deadlineYear
                ]
            );
        }

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

        $query->andWhere(['nc2.active' => true]);
    }

    /**
     * Getting search result when bad data
     *
     * @return mixed
     */
    protected function getResultWhenBadData()
    {
        $dataProvider = new ArrayDataProvider(['allModels' => []]);
        $this->itemsCount = $dataProvider->getTotalCount();

        return $dataProvider;
    }

    /**
     * Getting search result by given query
     *
     * @param type $query
     * @param type $pageParam
     * @return mixed
     */
    protected function getResult($query, $pageParam)
    {
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
                    'area',
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

        $this->itemsCount = $dataProvider->getTotalCount();

        return $dataProvider;
    }

    /**
     * Getting position array
     */
    protected function getPositionArray()
    {
        if (isset($this->developer) && !empty($this->developer)) {
            $result = \app\models\NewbuildingComplex::find()
                ->where(['developer_id' => $this->developer])
                ->orderBy(['id' => SORT_DESC])
                ->onlyActive(true)
                ->indexBy('id')
                ->asArray()
                ->all();

            foreach ($result as $key => $newbuildingComplex) {
                $this->newbuildingComplexes[$key] = $newbuildingComplex['name'];
            }

            if (isset($this->newbuilding_complex) && !empty($this->newbuilding_complex)) {
                $result = \app\models\Newbuilding::find()
                    ->where(['newbuilding_complex_id' => $this->newbuilding_complex])
                    ->indexBy('id')
                    ->asArray()
                    ->all();

                foreach ($result as $key => $position) {
                    $this->positionArray[$key] = \Yii::$app->formatter->asCapitalize($position['name']);
                }
            }
        }
    }

    /**
     * Fill search modelattributes from array
     *
     * @param array $params
     * @param type $isReturnFalseWhenNoData
     * @return mixed
     */
    protected function fillAttributes($params, $isReturnFalseWhenNoData)
    {
        $form = (new \ReflectionClass($this))->getShortName();
        $selectedCity = CityLocation::get();

        if (!isset($params[$form])) {
            if(is_null($selectedCity)) {
                return false;
            } else {
                $this->region_id = $selectedCity->region->id;
                $this->city_id = $selectedCity->id;
                 
                return true;
            }
        }

        if ($isReturnFalseWhenNoData
            && (isset($params[$form]['developer']) && empty($params[$form]['developer']))
            && (isset($params[$form]['newbuilding_complex']) && empty($params[$form]['newbuilding_complex']))
            && (isset($params[$form]['roomsCount']) && empty($params[$form]['roomsCount']))
            && (isset($params[$form]['priceFrom']) && empty($params[$form]['priceFrom']))
            && (isset($params[$form]['priceTo']) && empty($params[$form]['priceTo']))
            && (isset($params[$form]['areaFrom']) && empty($params[$form]['areaFrom']))
            && (isset($params[$form]['areaTo']) && empty($params[$form]['areaTo']))
            && (isset($params[$form]['floorFrom']) && empty($params[$form]['floorFrom']))
            && (isset($params[$form]['floorTo']) && empty($params[$form]['floorTo']))
            && (isset($params[$form]['totalFloorFrom']) && empty($params[$form]['totalFloorFrom']))
            && (isset($params[$form]['totalFloorTo']) && empty($params[$form]['totalFloorTo']))
            && (isset($params[$form]['material']) && empty($params[$form]['material']))
            && (isset($params[$form]['newbuilding_status']) && empty($params[$form]['newbuilding_status']))
            && (isset($params[$form]['region_id']) && empty($params[$form]['region_id']))
            && (isset($params[$form]['city_id']) && empty($params[$form]['city_id']))
            && (isset($params[$form]['district']) && empty($params[$form]['district']))
            && (isset($params[$form]['street_name']) && empty($params[$form]['street_name']))
            && (isset($params[$form]['flatType']) && empty($params[$form]['flatType']))
            && (isset($params[$form]['priceType']) && empty($params[$form]['priceType']))
            && (isset($params[$form]['deadlineYear']) && empty($params[$form]['deadlineYear']))
        ) {
            return false;
        }
        
        if (isset($params[$form]['region_id'])) {
            $this->region_id = $params[$form]['region_id'];
        }

        if (isset($params[$form]['city_id'])) {
            $this->city_id = $params[$form]['city_id'];
        } else {
            $this->city_id = $selectedCity->id;

            if(is_null($this->region_id)) {
                $this->region_id = $selectedCity->region->id;
            }
        }

        if (isset($params[$form]['district'])) {
            $this->district = $params[$form]['district'];

            $district = District::findOne($this->district);
            if(!is_null($district)) {
                if(is_null($this->city_id)) {
                    $this->city_id = $district->city->id;
                }
                if(is_null($this->region_id)) {
                    $this->region_id = $district->city->region->id;
                }
            }
        }

        if (isset($params[$form]['street_name'])) {
            $this->street_name = $params[$form]['street_name'];
        }

        if (isset($params[$form]['developer'])) {
            $this->developer = $params[$form]['developer'];
        }

        if (isset($params[$form]['newbuilding_complex'])) {
            $this->newbuilding_complex = $params[$form]['newbuilding_complex'];
        }

        if (isset($params[$form]['newbuilding_array'])
            && is_array($params[$form]['newbuilding_array'])) {
            $this->newbuilding_array = $params[$form]['newbuilding_array'];
        }

        if (isset($params[$form]['roomsCount'])) {
            $this->roomsCount = $params[$form]['roomsCount'];
        }

        if (isset($params[$form]['priceFrom'])) {
            $this->priceFrom = $params[$form]['priceFrom'];
        }

        if (isset($params[$form]['priceTo'])) {
            $this->priceTo = $params[$form]['priceTo'];
        }

        if (isset($params[$form]['areaFrom'])) {
            $this->areaFrom = $params[$form]['areaFrom'];
        }

        if (isset($params[$form]['areaTo'])) {
            $this->areaTo = $params[$form]['areaTo'];
        }

        if (isset($params[$form]['floorFrom'])) {
            $this->floorFrom = $params[$form]['floorFrom'];
        }

        if (isset($params[$form]['floorTo'])) {
            $this->floorTo = $params[$form]['floorTo'];
        }

        if (isset($params[$form]['totalFloor'])) {
            $this->totalFloor = $params[$form]['totalFloor'];
        }

        if (isset($params[$form]['material'])) {
            $this->material = $params[$form]['material'];
        }

        if (isset($params[$form]['newbuilding_status'])) {
            $this->newbuilding_status = $params[$form]['newbuilding_status'];
        }

        if (isset($params[$form]['update_date'])) {
            $this->update_date = $params[$form]['update_date'];
        }
        
        if(isset($params[$form]['flatType'])) {
            $this->flatType = $params[$form]['flatType'];
        }

        if(isset($params[$form]['priceType'])) {
            $this->priceType = $params[$form]['priceType'];
        }

        if(isset($params[$form]['totalFloorFrom'])) {
            $this->totalFloorFrom = $params[$form]['totalFloorFrom'];
        }

        if(isset($params[$form]['totalFloorTo'])) {
            $this->totalFloorTo = $params[$form]['totalFloorTo'];
        }

        if(isset($params[$form]['deadlineYear'])) {
            $this->deadlineYear = $params[$form]['deadlineYear'];
        }

        return true;
    }
}
