<?php

namespace app\models\query;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\app\models\Entrance]].
 */
class EntranceQuery extends ActiveQuery
{
    /**
     * Get entrances for given newbuilding
     * 
     * @param int $id newbuilding ID
     * @return yii\db\ActiveQuery
     */
    public function forNewbuilding($id)
    {
        return $this->where(['newbuilding_id' => $id]);
    }
}
