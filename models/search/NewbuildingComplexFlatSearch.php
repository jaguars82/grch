<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Flat;

/**
 * NewbuildingComplexFlatSearch represents the model behind the search form of `app\models\Flat`.
 */
class NewbuildingComplexFlatSearch extends Flat
{
    public $newbuilding_array = NULL;
    public $priceFrom = NULL, $priceTo = NULL;
    public $areaFrom = NULL, $areaTo = NULL;
    public $floorFrom = NULL, $floorTo = NULL, $totalFloor = NULL;
    public $material = NULL, $newbuilding_status, $deadline, $update_date;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['floorFrom', 'floorTo', 'rooms'], 'integer'],
            [['priceFrom', 'priceTo', 'areaFrom', 'areaTo'], 'double'],
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
            'priceFrom' => 'Цена',
            'priceTo' => 'Цена',
            'areaFrom' => 'Площадь',
            'areaTo' => 'Площадь',
            'floorFrom' => 'Этаж',
            'floorTo' => 'Этаж',
            'totalFloor' => 'Этажей не более',
            'newbuilding_status' => 'Сдан',
            'deadline' => 'Дата сдачи',
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
    public function search($newbuildingComplex, $params)
    {     
        $query = $newbuildingComplex
            ->getFlats()
            ->onlyActive()
            ->with(['flatImages', 'developer', 'favorites'])
            ->limit(5);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => [
                'attributes' => ['id'],
		'defaultOrder' => ['id' => SORT_DESC],
            ],
        ]);
     
        $this->fillAttributes($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

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
        
        if (isset($this->newbuilding_array) && count($this->newbuilding_array)) {
            $query->andWhere(['IN', 'newbuilding_id', $this->newbuilding_array]);
        }
        
        if (isset($this->update_date) && !empty($this->update_date)) {
            $updateDate = \Yii::$app->formatter->asDate($this->update_date, 'php:Y-m-d H:i:s');
            $query->andWhere("flat.updated_at != '0000-00-00 00:00:00'")
                ->andWhere(['>= ', 'flat.updated_at', $updateDate]);
        }
        
        if ((isset($this->totalFloor) && !empty($this->totalFloor))
            | (isset($this->material) && !empty($this->material))
            | (isset($this->newbuilding_status) && !empty($this->newbuilding_status))
            | (isset($this->deadline) && !empty($this->deadline))
        ) {
            $query->join('INNER JOIN', 'newbuilding', 'newbuilding.id = flat.newbuilding_id');
            
            if (isset($this->totalFloor) && !empty($this->totalFloor)) {
                $query->andWhere("total_floor <= :total_floor", [':total_floor' => $this->totalFloor]);
            }
            
            if (isset($this->material) && !empty($this->material)) {
                $query->andWhere(['material' => $this->material]);
            }

            if (isset($this->newbuilding_status) && !empty($this->newbuilding_status)) {
                $query->andWhere(['newbuilding.status' => $this->newbuilding_status]);
            }

            if (isset($this->deadline) && !empty($this->deadline)) {
                $deadline = \Yii::$app->formatter->asDate($this->deadline, 'php:Y-m-d H:i:s');
                $query->andWhere(['deadline' => $deadline]);
            }
        }
        
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
        if (!isset($params['NewbuildingComplexFlatSearch'])) {
            return;
        }
        
        if (isset($params['NewbuildingComplexFlatSearch']['rooms'])) {
            $this->rooms = $params['NewbuildingComplexFlatSearch']['rooms'];
        }
        
        if (isset($params['NewbuildingComplexFlatSearch']['priceFrom'])) {
            $this->priceFrom = $params['NewbuildingComplexFlatSearch']['priceFrom'];
        }
        
        if (isset($params['NewbuildingComplexFlatSearch']['priceTo'])) {
            $this->priceTo = $params['NewbuildingComplexFlatSearch']['priceTo'];
        }
        
        if (isset($params['NewbuildingComplexFlatSearch']['areaFrom'])) {
            $this->areaFrom = $params['NewbuildingComplexFlatSearch']['areaFrom'];
        }
        
        if (isset($params['NewbuildingComplexFlatSearch']['areaTo'])) {
            $this->areaTo = $params['NewbuildingComplexFlatSearch']['areaTo'];
        }
        
        if (isset($params['NewbuildingComplexFlatSearch']['floorFrom'])) {
            $this->floorFrom = $params['NewbuildingComplexFlatSearch']['floorFrom'];
        }
        
        if (isset($params['NewbuildingComplexFlatSearch']['floorTo'])) {
            $this->floorTo = $params['NewbuildingComplexFlatSearch']['floorTo'];
        }
        
        if (isset($params['NewbuildingComplexFlatSearch']['newbuilding_array']) 
            && is_array($params['NewbuildingComplexFlatSearch']['newbuilding_array'])) {
            $this->newbuilding_array = $params['NewbuildingComplexFlatSearch']['newbuilding_array'];
        }
        
        if (isset($params['NewbuildingComplexFlatSearch']['totalFloor'])) {
            $this->totalFloor = $params['NewbuildingComplexFlatSearch']['totalFloor'];
        }
        
        if (isset($params['NewbuildingComplexFlatSearch']['material'])) {
            $this->material = $params['NewbuildingComplexFlatSearch']['material'];
        }
        
        if (isset($params['NewbuildingComplexFlatSearch']['newbuilding_status'])) {
            $this->newbuilding_status = $params['NewbuildingComplexFlatSearch']['newbuilding_status'];
        }
        
        if (isset($params['NewbuildingComplexFlatSearch']['deadline'])) {
            $this->deadline = $params['NewbuildingComplexFlatSearch']['deadline'];
        }
        
        if (isset($params['NewbuildingComplexFlatSearch']['update_date'])) {
            $this->update_date = $params['NewbuildingComplexFlatSearch']['update_date'];
        }
    }
}
