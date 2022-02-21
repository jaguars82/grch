<?php

namespace app\models\service;

use Yii;
use app\models\FurnishImage;

/**
 * This is the model class for processing furnishes.
 */
class Furnish extends \app\models\Furnish
{
    /**
     * Create furnish's entities
     * 
     * @param array $data
     * @return \app\models\service\Furnish
     * @throws \Exception
     */
    public static function create($data)
    {
        $transaction = Yii::$app->db->beginTransaction();
            
        try {
            $furnish = new Furnish();
            $furnish->fill($data);
            $furnish->save();

            foreach ($data['images'] as $image) {
                $furnishImage = new FurnishImage([
                    'image' => $image,
                ]);
                $furnish->link('furnishImages', $furnishImage);
            }

            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
                
        return $furnish;
    }
    
    /**
     * Edit furnish's entities
     * 
     * @param array $data
     * @throws \Exception
     */
    public function edit($data)
    {
        if (isset($data['newbuilding_complex_id'])) {
            unset($data['newbuilding_complex_id']);
        }
        
        $transaction = Yii::$app->db->beginTransaction();
            
        try {           
            $this->fill($data);
            $this->save();

            foreach ($this->furnishImages as $furnishImages) {
                if (!in_array(strval($furnishImages->id), $data['savedImages'])) {
                    $furnishImages->delete();
                }
            }

            foreach ($data['images'] as $image) {
                $furnishImage = new FurnishImage([
                    'image' => $image,
                ]);
                $this->link('furnishImages', $furnishImage);
            }

            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
}
