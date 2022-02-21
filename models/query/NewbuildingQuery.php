<?php

namespace app\models\query;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\app\models\Newbuilding]].
 */
class NewbuildingQuery extends ActiveQuery
{
    /**
     * Get newbuildings for given newbuilding complex
     * 
     * @param int $id developer ID
     * @return yii\db\ActiveQuery
     */
    public function forNewbuildingComplex($id)
    {
        return $this->where(['newbuilding_complex_id' => $id]);
    }
}
