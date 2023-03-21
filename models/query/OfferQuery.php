<?php

namespace app\models\query;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\app\models\Offer]].
 */
class OfferQuery extends ActiveQuery
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
}
