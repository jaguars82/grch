<?php

namespace app\models\query;

use app\models\Flat;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\app\models\Favorite]].
 */
class FavoriteQuery extends ActiveQuery
{      
    /**
     * Get favorite flats for current user
     * 
     * @return yii\db\ActiveQuery
     */
    public function forCurrentUser()
    {
        $user = \Yii::$app->user;
        
        return $this->andWhere("user_id = {$user->id}");
    }
    
    /**
     * Get favorite flats for current user
     * 
     * @return yii\db\ActiveQuery
     */
    public function onlyActive()
    {
        return $this->andWhere('archived_by IS NULL');
    }
    
    /**
     * Get favorite flats for current user
     * 
     * @return yii\db\ActiveQuery
     */
    public function onlyArchive()
    {     
        return $this->andWhere('archived_by IS NOT NULL');
    }
    
    /**
     * Get favorite sold flats
     * 
     * @return yii\db\ActiveQuery
     */
    public function forSoldFlats()
    {
        return $this->join('INNER JOIN', 'flat', 'flat.id = favorite.flat_id')
                ->andWhere(['status' => Flat::STATUS_SOLD]);
    }
}
