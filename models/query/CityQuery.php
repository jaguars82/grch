<?php

namespace app\models\query;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\app\models\City]].
 *
 * @see \app\models\Office
 */
class CityQuery extends ActiveQuery
{
    public function forRegion($id)
    {
        return $this->where(['region_id' => $id]);
    }
}