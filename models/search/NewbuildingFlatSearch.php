<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Flat;

/**
 * NewbuildingFlatSearch represents the model behind the search form of `app\models\Flat`.
 */
class NewbuildingFlatSearch extends Flat
{
    public $priceFrom = NULL, $priceTo = NULL;
    public $areaFrom = NULL, $areaTo = NULL;
    public $floorFrom = NULL, $floorTo = NULL;
    public $update_date;
    
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
            'priceFrom' => 'Цена',
            'priceTo' => 'Цена',
            'areaFrom' => 'Площадь',
            'areaTo' => 'Площадь',
            'floorFrom' => 'Этаж',
            'floorTo' => 'Этаж',
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
    public function search($newbuilding, $params)
    {     
        $query = $newbuilding
            ->getFlats()
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
        
        if (isset($this->update_date) && !empty($this->update_date)) {
            $updateDate = \Yii::$app->formatter->asDate($this->update_date, 'php:Y-m-d H:i:s');
            $query->andWhere("flat.updated_at != '0000-00-00 00:00:00'")
                ->andWhere(['>= ', 'flat.updated_at', $updateDate]);
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
        if (!isset($params['NewbuildingFlatSearch'])) {
            return;
        }
        
        if (isset($params['NewbuildingFlatSearch']['rooms'])) {
            $this->rooms = $params['NewbuildingFlatSearch']['rooms'];
        }
        
        if (isset($params['NewbuildingFlatSearch']['priceFrom'])) {
            $this->priceFrom = $params['NewbuildingFlatSearch']['priceFrom'];
        }
        
        if (isset($params['NewbuildingFlatSearch']['priceTo'])) {
            $this->priceTo = $params['NewbuildingFlatSearch']['priceTo'];
        }
        
        if (isset($params['NewbuildingFlatSearch']['areaFrom'])) {
            $this->areaFrom = $params['NewbuildingFlatSearch']['areaFrom'];
        }
        
        if (isset($params['NewbuildingFlatSearch']['areaTo'])) {
            $this->areaTo = $params['NewbuildingFlatSearch']['areaTo'];
        }
        
        if (isset($params['NewbuildingFlatSearch']['floorFrom'])) {
            $this->floorFrom = $params['NewbuildingFlatSearch']['floorFrom'];
        }
        
        if (isset($params['NewbuildingFlatSearch']['floorTo'])) {
            $this->floorTo = $params['NewbuildingFlatSearch']['floorTo'];
        }
        
        if (isset($params['NewbuildingFlatSearch']['update_date'])) {
            $this->update_date = $params['NewbuildingFlatSearch']['update_date'];
        }
    }
}
