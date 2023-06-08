<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Developer;

/**
 * DeveloperSearch represents the model behind the search form of `app\models\Developer`.
 */
class DeveloperSearch extends Developer
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
        // if user is a developer representative - show only for his developer
        if(\Yii::$app->user->identity->role === 'developer_repres') {
            $query = Developer::find()->where(['id' => \Yii::$app->user->identity->developer_id]);
        } else {
            // show for all developers
            $query = Developer::find();
        }
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSizeParam' => false,
                'pageSize' => 8,
            ],
            'sort' => [
                'attributes' => ['sort', 'name', 'id'],
		        'defaultOrder' => [
                    'sort' => SORT_ASC,
                    'name' => SORT_DESC
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'name', $this->name]);
        
        $this->itemsCount = $dataProvider->getTotalCount();
        
        return $dataProvider;
    }
}
