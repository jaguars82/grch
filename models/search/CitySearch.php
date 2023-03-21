<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\City;

/**
 * CitySearch represents the model behind the search form of `app\models\City`.
 */
class CitySearch extends City
{
    public $itemsCount;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['region_id', 'region_district_id'], 'safe'],
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
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = City::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'region_id' => $this->region_id,
            'region_district_id' => $this->region_district_id,
        ]);

        $this->itemsCount += $dataProvider->getTotalCount();

        return $dataProvider;
    }
}
