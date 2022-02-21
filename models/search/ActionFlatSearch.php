<?php

namespace app\models\search;

use app\models\Flat;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\Expression;

/**
 * ActionFlatSearch represents the model behind the search actions for `app\models\News`.
 */
class ActionFlatSearch extends Flat
{
    const SUCCESS = 10;
    const ERROR_COMMON = 20;
    const ERROR_HAVE_DISCOUNT = 21;

    public $developer, $newbuilding_complex;
    public $newbuilding_array = NULL;
    public $priceFrom = NULL, $priceTo = NULL;
    public $areaFrom = NULL, $areaTo = NULL;
    public $floorFrom = NULL, $floorTo = NULL, $totalFloor = NULL;
    public $material = NULL, $newbuilding_status, $deadline, $update_date;

    public $newbuildingComplexes = [];
    public $positionArray = [];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['floorFrom', 'floorTo', 'totalFloor', 'rooms', 'developer', 'newbuilding_complex'], 'integer'],
            [['priceFrom', 'priceTo', 'areaFrom', 'areaTo'], 'double'],
            [['material', 'deadline', 'update_date'], 'string'],
            ['newbuilding_status', 'safe']
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
            'newbuilding_array' => 'Позиции',
            'priceFrom' => 'Стоимость',
            'areaFrom' => 'Площадь',
            'floorFrom' => 'Этаж',
            'totalFloor' => 'Этажей не более',
            'newbuilding_status' => 'Сдан',
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
    public function search($params)
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
            ->with(['flatImages']);

        if (!$this->fillAttributes($params) || !$this->validate()) {
            return $this->getResultWhenBadData();
        }

        $query->andWhere(['>', 'price_cash', 0]);

        $this->applyFilters($query);

        $this->getPositionArray();

        $haveDiscount = $this->checkFlatData($query);

        $query->limit(5);

        if ($haveDiscount) {
            return $this->getResultWhenHaveDiscount();
        }

        return [$this->getResult($query), self::SUCCESS];
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

        if (isset($this->rooms) && !empty($this->rooms)) {
            if ($this->rooms == 5) {
                $query->andWhere(['>=', 'rooms', $this->rooms]);
            } else {
                $query->andWhere(['rooms' => $this->rooms]);
            }
        }

        if (isset($this->priceFrom) && !empty($this->priceFrom)) {
            $query->andWhere("price_cash >= :price_from", [':price_from' => $this->priceFrom]);
        }

        if (isset($this->priceTo) && !empty($this->priceTo)) {
            $query->andWhere("price_cash <= :price_to", [':price_to' => $this->priceTo]);
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

        if (isset($this->totalFloor) && !empty($this->totalFloor)) {
            $query->andWhere("n1.total_floor <= :total_floor", [':total_floor' => $this->totalFloor]);
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

        if (isset($this->deadline) && !empty($this->deadline)) {
            $deadline = \Yii::$app->formatter->asDate($this->deadline, 'php:Y-m-d H:i:s');
            $query->andWhere(['n1.deadline' => $deadline]);
        }

        if (isset($this->developer) && !empty($this->developer)) {
            $query->andWhere(['nc2.developer_id' => $this->developer]);
        }
    }

    /**
     * Getting search result when bad data
     *
     * @return mixed
     */
    protected function getResultWhenBadData()
    {
        $dataProvider = new ArrayDataProvider(['allModels' => []]);

        return [$dataProvider, self::ERROR_COMMON];
    }


    protected function getResultWhenHaveDiscount()
    {
        $dataProvider = new ArrayDataProvider(['allModels' => []]);

        return [$dataProvider, self::ERROR_HAVE_DISCOUNT];
    }

    /**
     * Getting search result by given query
     *
     * @param type $query
     * @return mixed
     */
    protected function getResult($query)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => false,
        ]);

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
     * @return mixed
     */
    protected function fillAttributes($params)
    {
        $form = (new \ReflectionClass($this))->getShortName();

        if (!isset($params[$form])) {
            return false;
        }

        if ((isset($params[$form]['developer']) && empty($params[$form]['developer']))
            && (isset($params[$form]['newbuilding_complex']) && empty($params[$form]['newbuilding_complex']))
            && (isset($params[$form]['rooms']) && empty($params[$form]['rooms']))
            && (isset($params[$form]['priceFrom']) && empty($params[$form]['priceFrom']))
            && (isset($params[$form]['priceTo']) && empty($params[$form]['priceTo']))
            && (isset($params[$form]['areaFrom']) && empty($params[$form]['areaFrom']))
            && (isset($params[$form]['areaTo']) && empty($params[$form]['areaTo']))
            && (isset($params[$form]['floorFrom']) && empty($params[$form]['floorFrom']))
            && (isset($params[$form]['floorTo']) && empty($params[$form]['floorTo']))
            && (isset($params[$form]['totalFloor']) && empty($params[$form]['totalFloor']))
            && (isset($params[$form]['material']) && empty($params[$form]['material']))
            && (isset($params[$form]['newbuilding_status']) && empty($params[$form]['newbuilding_status']))
            && (isset($params[$form]['deadline']) && empty($params[$form]['deadline']))
            && (isset($params[$form]['update_date']) && empty($params[$form]['update_date']))
        ) {
            return false;
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

        if (isset($params[$form]['rooms'])) {
            $this->rooms = $params[$form]['rooms'];
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

        if (isset($params[$form]['deadline'])) {
            $this->deadline = $params[$form]['deadline'];
        }

        if (isset($params[$form]['update_date'])) {
            $this->update_date = $params[$form]['update_date'];
        }

        return true;
    }

    public function getFlatFilter() {
        $values = [
            'rooms' => $this->rooms,
            'totalFloor' => $this->totalFloor,
            'floorFrom' => $this->floorFrom,
            'floorTo' => $this->floorTo,
            'priceFrom' => $this->priceFrom,
            'priceTo' => $this->priceTo,
            'areaFrom' => $this->areaFrom,
            'areaTo' => $this->areaTo,
            'material' => $this->material,
            'deadline' => $this->deadline,
            'update_date' => $this->update_date,
            'developer' => $this->developer,
            'newbuilding_complex' => $this->newbuilding_complex,
            'newbuilding_status' => $this->newbuilding_status,
            'newbuilding_array' => $this->newbuilding_array
        ];


        return $values;
    }



    private function checkFlatData($query)
    {
        $flatList = $query->all();

        foreach ($flatList as $flat) {
            if ($flat->discount > 0) {
                return true;
            } 
        }

        return false;
    }

    public function setDiscount($discount)
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
            ->with(['flatImages']);

        $query->andWhere(['>', 'price_cash', 0]);

        $this->applyFilters($query);


        $flatList = $query->all();

        $flatDiscount = $discount / 100;
        foreach ($flatList as $flat) {
            $flat->discount = $flatDiscount;
            $flat->save();
        }
    }
}
