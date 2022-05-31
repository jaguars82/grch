<?php

namespace app\models\service;

/**
 * This is the model class for processing newbuilding.
 */
class Entrance extends \app\models\Entrance
{    
    /**
     * Create entrance entities
     * 
     * @param array $newbuildingData
     * @return \app\models\service\Entrance
     * @throws \Exception
     */
    public static function create($entranceData)
    {
        $transaction = \Yii::$app->db->beginTransaction();
            
        try {
            $entrance = (new Entrance())->fill($entranceData);
            $entrance->save();

            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $entrance;
    }
    
    /**
     * Edit entrance entities
     * 
     * @param array $entranceData
     * @throws \Exception
     */
    public function edit($entranceData)
    {
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            $this->fill($entranceData);
            $this->save();

            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
}
