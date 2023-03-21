<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\News;

/**
 * NewsSearch represents the model behind the search form of `app\models\News`.
 */
class NewsSearch extends News
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'category'], 'integer'],
            [['title', 'detail', 'image', 'created_at', 'updated_at'], 'safe'],
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
        $query = News::find()->where(['is_archived' => 0])->with(['newsFiles', 'newbuildingComplexes.developer']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageParam' => 'all-page',
                'pageSizeParam' => false,
                'pageSize' => 5,
            ],
            'sort' => [
                'attributes' => ['created_at'],
		'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'category' => $this->category,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'detail', $this->detail])
            ->andFilterWhere(['like', 'image', $this->image]);
        
        $query->forNewbuildingComplex(isset($params['newbuilding-complex']) ? $params['newbuilding-complex'] : null)
            ->forDeveloper(isset($params['developer']) ? $params['developer'] : null);

        return $dataProvider;
    }
}
