<?php

namespace app\models\search;

use app\models\Flat;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

/**
 * SimpleFlatSearch represents the model behind the search form of `app\models\Flat`.
 */
class SimpleFlatSearch extends Flat
{
    public $itemsCount;

    public $address, $district_id, $developer, $newbuilding_complex;
    public $priceFrom = NULL, $priceTo = NULL;
    public $areaFrom = NULL, $areaTo = NULL;

    public $newbuildingComplexes = [];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['rooms', 'developer', 'newbuilding_complex', 'district_id'], 'integer'],
            [['priceFrom', 'priceTo', 'areaFrom', 'areaTo'], 'double'],
            [['address'], 'string']
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
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $pageParam)
    {
        $query = Flat::find()
            ->select([
                'flat.*',
                'minCostWithDiscount' => new Expression('IF(price_credit is not null, LEAST(price_cash * (1 + discount), price_credit * (1 + discount)), price_cash * (1 + discount))'),
                'maxCostWithDiscount' => new Expression('IF(price_credit is not null, GREATEST(price_cash * (1 + discount), price_credit * (1 + discount)), price_cash * (1 + discount))')
            ])
            ->onlyActive()
            ->joinWith(['developer d1', 'newbuildingComplex nc1'])
            ->andWhere(['nc1.active' => true])
            ->with(['flatImages', 'favorites']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageParam' => $pageParam,
                'pageSizeParam' => false,
                'pageSize' => 7,
            ],
            'sort' => [
                'attributes' => [
                    'costWithDiscount' => [
                        'label' => 'Стоимость',
                        'asc' => ['minCostWithDiscount' => SORT_ASC],
                        'desc' => ['maxCostWithDiscount' => SORT_DESC],
                    ],
                    'area',
                    'rooms' => [
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

        if (!$this->fillAttributes($params)) {
            $this->itemsCount = 0;
            $dataProvider->totalCount = 0;
            return $dataProvider;
        }

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['>=', 'area', $this->areaFrom])
            ->andFilterWhere(['<=', 'area', $this->areaTo]);

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

        if ((isset($this->district_id) && !empty($this->district_id)
            | (isset($this->newbuilding_complex) && !empty($this->newbuilding_complex))
            | (isset($this->developer) && !empty($this->developer)))
        ) {
            $query->join('INNER JOIN', 'newbuilding as n1', 'n1.id = flat.newbuilding_id');

            if (isset($this->newbuilding_complex) && !empty($this->newbuilding_complex)) {
                $query->andWhere(['n1.newbuilding_complex_id' => $this->newbuilding_complex]);
            }

            if ((isset($this->district_id) && !empty($this->district_id))
                | (isset($this->developer) && !empty($this->developer))
            ) {
                $query->join('INNER JOIN', 'newbuilding_complex as nc2', 'nc2.id = n1.newbuilding_complex_id');

                if (isset($this->district_id) && !empty($this->district_id)) {
                    $query->andWhere(['nc2.district_id' => $this->district_id]);
                }

                if (isset($this->developer) && !empty($this->developer)) {
                    $query->andWhere(['nc2.developer_id' => $this->developer]);
                }
            }
        }

        if (isset($this->developer) && !empty($this->developer)) {
            $result = \app\models\NewbuildingComplex::find()
                ->where(['developer_id' => $this->developer])
                ->onlyActive(true)
                ->orderBy(['id' => SORT_DESC])
                ->indexBy('id')
                ->asArray()
                ->all();

            foreach ($result as $key => $newbuildingComplex) {
                $this->newbuildingComplexes[$key] = $newbuildingComplex['name'];
            }
        }

		$query->andWhere(['>', 'price_cash', 0]);

        $this->itemsCount = $dataProvider->getTotalCount();

        return $dataProvider;
    }

    /**
     * Fill search model attributes from array
     *
     * @param array $params
     * @return mixed
     */
    private function fillAttributes($params)
    {
        if (!isset($params['SimpleFlatSearch'])) {
            return false;
        }

        if ((isset($params['SimpleFlatSearch']['district_id']) && empty($params['SimpleFlatSearch']['district_id']))
            && (isset($params['SimpleFlatSearch']['developer']) && empty($params['SimpleFlatSearch']['developer']))
            && (isset($params['SimpleFlatSearch']['newbuilding_complex']) && empty($params['SimpleFlatSearch']['newbuilding_complex']))
            && (isset($params['SimpleFlatSearch']['rooms']) && empty($params['SimpleFlatSearch']['rooms']))
            && (isset($params['SimpleFlatSearch']['priceFrom']) && empty($params['SimpleFlatSearch']['priceFrom']))
            && (isset($params['SimpleFlatSearch']['priceTo']) && empty($params['SimpleFlatSearch']['priceTo']))
        ) {
            return false;
        }

        if (isset($params['SimpleFlatSearch']['district_id'])) {
            $this->district_id = $params['SimpleFlatSearch']['district_id'];
        }

        if (isset($params['SimpleFlatSearch']['developer'])) {
            $this->developer = $params['SimpleFlatSearch']['developer'];
        }

        if (isset($params['SimpleFlatSearch']['newbuilding_complex'])) {
            $this->newbuilding_complex = $params['SimpleFlatSearch']['newbuilding_complex'];
        }

        if (isset($params['SimpleFlatSearch']['rooms'])) {
            $this->rooms = $params['SimpleFlatSearch']['rooms'];
        }

        if (isset($params['SimpleFlatSearch']['priceFrom'])) {
            $this->priceFrom = $params['SimpleFlatSearch']['priceFrom'];
        }

        if (isset($params['SimpleFlatSearch']['priceTo'])) {
            $this->priceTo = $params['SimpleFlatSearch']['priceTo'];
        }

        if (isset($params['SimpleFlatSearch']['areaFrom'])) {
            $this->areaFrom = $params['SimpleFlatSearch']['areaFrom'];
        }

        if (isset($params['SimpleFlatSearch']['areaTo'])) {
            $this->areaTo = $params['SimpleFlatSearch']['areaTo'];
        }

        return true;
    }
}
