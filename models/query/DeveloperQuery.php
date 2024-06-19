<?php

namespace app\models\query;

use app\models\Flat;
use app\models\Import;
use yii\db\ActiveQuery;
use yii\db\conditions\InCondition;

/**
 * This is the ActiveQuery class for [[\app\models\Developer]].
 */
class DeveloperQuery extends ActiveQuery
{
    /**
     * Get developers only with active flats
     * 
     * @return yii\db\ActiveQuery
     */
    public function onlyWithActiveFlats($isOnlyActiveFlats = true)
    {
        if (!$isOnlyActiveFlats) {
            return $this;
        }
        
        return $this->join('INNER JOIN', 'newbuilding_complex as nb1', 'developer.id = nb1.developer_id')
                ->join('INNER JOIN', 'newbuilding', 'nb1.id = newbuilding.newbuilding_complex_id')
                ->join('INNER JOIN', 'flat', 'newbuilding.id = flat.newbuilding_id')
                ->andWhere(['flat.status' => Flat::STATUS_SALE]);
    }
    
    /**
     * Get developers only with expired automatic import
     * 
     * @return yii\db\ActiveQuery
     */
    public function onlyWithExpiredImport()
    {
        return $this->join('INNER JOIN', 'import', 'import.id = developer.import_id')
                ->andWhere(new InCondition('type', 'in', Import::$import_auto))
                ->andWhere('TO_SECONDS(NOW()) - TO_SECONDS(imported_at) >= schedule OR imported_at IS NULL');
    }
    
    /**
     * Get developers which have newbuilding complexes
     * 
     * @return yii\db\ActiveQuery
     */
    public function whereNewbuildingComplexesExist()
    {
        return $this->join('LEFT JOIN', 'newbuilding_complex', 'developer.id = newbuilding_complex.developer_id')
			->where(['newbuilding_complex.active' => 1])
            ->groupBy('developer.id')
            ->having('count(newbuilding_complex.developer_id) > 0');
    }
}
