<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use app\models\Agency;

/**
 * AgencyContactSearch represents the model behind the search form of `app\models\Agency` contacts.
 */
class AgencyContactSearch extends Agency
{
    public $itemsCount;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'safe'],
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
        $this->load($params);

        if (!$this->validate()) {
            return;
        }
        
        $agencies = Agency::find()->with('contacts')->andFilterWhere(['like', 'name', $this->name])->all();        
        $contactsData = [];
        
        foreach ($agencies as $agency) {            
            $contactsData[] = [
                'id' => $agency->id,
                'name' => $agency->name,
                'provider' => new ArrayDataProvider([
                    'allModels' => $agency->contacts,
                    'sort' => [
                        'attributes' => ['id'], 
                        'defaultOrder' => ['id' => SORT_DESC]
                    ],
                ]),
            ];
        }
        
        $dataProvider = new ArrayDataProvider([
            'allModels' => $contactsData,
            'pagination' => [
                'pageSizeParam' => false,
                'pageSize' => 5
            ],
        ]);
        
        $this->itemsCount = $dataProvider->getTotalCount();

        return $dataProvider;
    }
}
