<?php

namespace app\models\search;

use app\models\Offer;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

/**
 * OfferSearch represents the model behind the search form of `app\models\Offer`.
 */
class OfferSearch extends Offer
{
    public $itemsCount;
    
    public $address, $dateFrom, $dateTo;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['address', 'dateFrom', 'dateTo'], 'string']
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
            'address' => 'Адрес',
            'dateFrom' => 'Дата создания',
            'dateTo' => 'Дата создания',
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
        $query = Offer::find()
            ->join('INNER JOIN', 'flat f1', 'f1.id = offer.flat_id')
            ->join('INNER JOIN', 'newbuilding n1', 'n1.id = f1.newbuilding_id')
            ->join('INNER JOIN', 'newbuilding_complex nc1', 'nc1.id = n1.newbuilding_complex_id')
            ->forCurrentUser();
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSizeParam' => false,
                'pageSize' => 9,
            ],
            'sort' => [
                'attributes' => [
                    'id' => [
                        'asc' => ['offer.id' => SORT_ASC],
                        'desc' => ['offer.id' => SORT_DESC],
                    ],
                    'flat_id' => [
                        'asc' => ['nc1.address' => SORT_ASC, 'f1.number' => SORT_ASC],
                        'desc' => ['nc1.address' => SORT_DESC, 'f1.number' => SORT_DESC],
                    ],
                    'created_at',
                ],
		'defaultOrder' => ['id' => SORT_DESC],
            ],
        ]);
             
        if (!$this->fillAttributes($params)) {
            $dataProvider->totalCount = 0;
        }

        if (!$this->validate()) {
            return $dataProvider;
        }

        if (isset($this->address) && !empty($this->address)) {
            $query->andWhere(['like', 'CONCAT(nc1.address, "№, ", f1.number)', $this->address]);
        }
        
        if (isset($this->dateFrom) && !empty($this->dateFrom)) {
            $dateFrom = \Yii::$app->formatter->asDate($this->dateFrom, 'php:Y-m-d H:i:s');
            $query->andWhere("offer.created_at != '0000-00-00 00:00:00'")
                ->andWhere(['>= ', 'offer.created_at', $dateFrom]);
        }
        
        if (isset($this->dateTo) && !empty($this->dateTo)) {
            $dateTo = \Yii::$app->formatter->asDate($this->dateTo, 'php:Y-m-d H:i:s');
            $query->andWhere("offer.created_at != '0000-00-00 00:00:00'")
                ->andWhere(['<= ', 'offer.created_at', $dateTo]);
        }
        
        $this->itemsCount = $dataProvider->getTotalCount();
        
        return $dataProvider;
    }
    
    /**
     * Fill search modelattributes from array
     * 
     * @param array $params
     * @return mixed
     */
    private function fillAttributes($params)
    {        
        if (isset($params['OfferSearch']) && isset($params['OfferSearch']['address'])) {
            $this->address = $params['OfferSearch']['address'];
        }
        
        if (isset($params['OfferSearch']) && isset($params['OfferSearch']['dateFrom'])) {
            $this->dateFrom = $params['OfferSearch']['dateFrom'];
        }
        
        if (isset($params['OfferSearch']) && isset($params['OfferSearch']['dateTo'])) {
            $this->dateTo = $params['OfferSearch']['dateTo'];
        }
        
        return true;
    }
}
