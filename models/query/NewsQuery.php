<?php

namespace app\models\query;

use yii\db\ActiveQuery;
use app\models\News;

/**
 * This is the ActiveQuery class for [[\app\models\News]].
 */
class NewsQuery extends ActiveQuery
{
    /**
     * Get only news
     * 
     * @return yii\db\ActiveQuery
     */
    public function onlyNews()
    {
        return $this->andWhere(['category' => News::CATEGORY_NEWS]);
    }
    
    /**
     * Get only actual entries
     * 
     * @return yii\db\ActiveQuery
     */
    public function onlyActual()
    {
        return $this->andWhere(['is_archived' => 0]);
    }
    
    /**
     * Get only actions
     * 
     * @param boolean $isActive flag indicating that news is active
     * @return yii\db\ActiveQuery
     */
    public function onlyActions($isActive = false)
    {
        $query= $this->andWhere(['category' => News::CATEGORY_ACTION]);
        //    ->andWhere(['is_archived' => 0]);
        if ($isActive) {
            // $query->join('INNER JOIN', 'action_data', 'news.id = action_data.news_id')
            $query->join('INNER JOIN', 'action_data', 'news.id = action_data.news_id')
                ->where('is_actual = 1');
        }
        
        return $query;
    }
    
    /**
     * Get news for given newbuilding complex
     * 
     * @param mixed $newbuildingComplex newbuilding complex ID
     * @return yii\db\ActiveQuery
     */
    public function forNewbuildingComplex($newbuildingComplex)
    {
        if (is_null($newbuildingComplex)) {
            return $this;
        }
        
        return $this->join('INNER JOIN', 'news_newbuilding_complex as a1', 'news.id = a1.news_id')
            ->andWhere('a1.newbuilding_complex_id=:newbuildingComplex', [':newbuildingComplex' => (int)$newbuildingComplex]);
    }
    
    /**
     * Get news for given developer
     * 
     * @param mixed $developer developer ID
     * @return yii\db\ActiveQuery
     */
    public function forDeveloper($developer)
    {
        if (is_null($developer)) {
            return $this;
        }
        
        return $this->join('INNER JOIN', 'news_newbuilding_complex as a2', 'news.id = a2.news_id')
            ->join('INNER JOIN', 'newbuilding_complex as a3', 'a2.newbuilding_complex_id = a3.id')
            ->andWhere('a3.developer_id=:developer', [':developer' => (int)$developer]);
    }
}
