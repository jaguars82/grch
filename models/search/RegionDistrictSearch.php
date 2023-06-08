<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\RegionDistrict;

/**
 * RegionDistrictSearch represents the model behind the search form of `app\models\RegionDistrict`.
 */
class RegionDistrictSearch extends RegionDistrict
{
    public $itemsCount;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['region_id'], 'safe'],
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
        $query = RegionDistrict::find();

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
            'region_id' => $this->region_id
        ]);

        $this->itemsCount += $dataProvider->getTotalCount();

        return $dataProvider;
    }
}
