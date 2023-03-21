<?php

namespace app\models\query;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\app\models\District]].
 *
 * @see \app\models\District
 */
class DistrictQuery extends ActiveQuery
{
    public function forCity($id)
    {
        return $this->where(['city_id' => $id]);
    }
}