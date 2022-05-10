<?php

namespace app\models\search;

use app\models\Developer;
use app\models\NewbuildingComplex;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;

/**
 * NewbuildingComplexSearch represents the model behind the search form of `app\models\NewbuildingComplex`.
 */
class NewbuildingComplexSearch extends NewbuildingComplex
{
    public $itemsCount;
    public $only_active = false;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'developer_id', 'only_active'], 'safe'],
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
            'only_active' => 'Только с активными предложениями',
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $onlyActiveNewbuildingComplex = true)
    {
        $this->load($params);

        if (isset($params['NewbuildingComplexSearch']) && isset($params['NewbuildingComplexSearch']['only_active'])) {
            $this->only_active = (boolean)$params['NewbuildingComplexSearch']['only_active'];
        }

        if (!$this->validate()) {
            return; 
        }

        $developers = Developer::find()->whereNewbuildingComplexesExist()
                ->onlyWithActiveFlats($this->only_active)
                ->andFilterWhere(['developer.id' => $this->developer_id])
                ->andFilterWhere(['like', 'newbuilding_complex.name', $this->name])
                ->orderBy(['sort' => SORT_ASC])
                ->all();
                
        $developersData = [];
        
        $this->itemsCount = 0;
        
        foreach ($developers as $developer) {            
            $developerData = [
                'id' => $developer->id,
                'name' => $developer->name,
                'provider' => new ActiveDataProvider([
                    'query' => $developer
                        ->getNewbuildingComplexes()
                        ->onlyActive($onlyActiveNewbuildingComplex)
                        ->onlyWithActiveBuildings()
                        ->onlyWithActiveFlats($this->only_active)
                        ->andFilterWhere(['like', 'newbuilding_complex.name', $this->name])
                        ->with(['actions', 'flatsWithDiscount', 'activeActions']),
                    'pagination' => false,
                ]),
            ];
            
            $developersData[] = $developerData;
            $this->itemsCount += $developerData['provider']->getTotalCount();
        }
        
        $dataProvider = new ArrayDataProvider([
            'allModels' => $developersData,
            'pagination' => [
                'pageSizeParam' => false,
                'pageSize' => 3, 
            ],
        ]);

        return $dataProvider;
    }
}
