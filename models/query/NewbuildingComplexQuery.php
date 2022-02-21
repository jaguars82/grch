<?php

namespace app\models\query;

use app\models\Flat;
use app\models\Import;
use yii\db\ActiveQuery;
use yii\db\conditions\InCondition;

/**
 * This is the ActiveQuery class for [[\app\models\NewbuildingComplex]].
 */
class NewbuildingComplexQuery extends ActiveQuery
{
    /**
     * Get newbuilding complexes for given developer
     * 
     * @param int $id developer ID
     * @return yii\db\ActiveQuery
     */
    public function forDeveloper($id)
    {
        return $this->where(['developer_id' => $id]);
    }
    
    public function onlyActive($isOnlyActive = true)
    {
        if(!$isOnlyActive) {
            return $this;
        }
        
        return $this->andWhere(['active' => true]);
    }

    /**
     * Get newbuilding complexes only with active flats
     * 
     * @return yii\db\ActiveQuery
     */
    public function onlyWithActiveFlats($isOnlyActiveFlats = true)
    {
        if (!$isOnlyActiveFlats) {
            return $this;
        }
        
        return $this->join('INNER JOIN', 'newbuilding', 'newbuilding_complex.id = newbuilding.newbuilding_complex_id')
                ->join('INNER JOIN', 'flat', 'newbuilding.id = flat.newbuilding_id')
                ->andWhere(['flat.status' => Flat::STATUS_SALE]);
    }
    
    /**
     * Get newbuilding complexes only with expired automatic import
     * 
     * @return yii\db\ActiveQuery
     */
    public function onlyWithExpiredImport()
    {
        return $this->join('INNER JOIN', 'import', 'import.id = newbuilding_complex.import_id')
                ->andWhere(new InCondition('type', 'in', Import::$import_auto))
                ->andWhere('TO_SECONDS(NOW()) - TO_SECONDS(imported_at) >= schedule OR imported_at IS NULL');
    }
}
