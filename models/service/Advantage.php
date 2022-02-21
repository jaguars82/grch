<?php

namespace app\models\service;

use Yii;

/**
 * This is the model class for processing advantages.
 */
class Advantage extends \app\models\Advantage
{
    /**
     * Create advantages's entities
     * 
     * @param array $data
     * @return \app\models\service\Advantage
     * @throws \Exception
     */
    public static function create($data)
    {
        $transaction = Yii::$app->db->beginTransaction();
            
        try {
            $advantage = new Advantage();
            $advantage->fill($data);
            $advantage->save();

            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
                
        return $advantage;
    }
    
    /**
     * Edit advantage's entities
     * 
     * @param array $data
     * @throws \Exception
     */
    public function edit($data)
    {
        $transaction = Yii::$app->db->beginTransaction();
            
        try {           
            $this->fill($data, ['icon']);

            if($data['is_icon_reset']) {
                $this->icon = NUll;
            } else {
                $this->icon = (!is_null($data['icon'])) ? $data['icon'] : $this->icon;
            }

            $this->save();

            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
}
