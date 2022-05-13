<?php

namespace app\models\service;

use app\models\Advantage;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for processing newbuilding.
 */
class Newbuilding extends \app\models\Newbuilding
{    
    /**
     * Create newbuilding entities
     * 
     * @param array $newbuildingData
     * @return \app\models\service\Newbuilding
     * @throws \Exception
     * @throws NotFoundHttpException
     */
    public static function create($newbuildingData)
    {
        $transaction = \Yii::$app->db->beginTransaction();
            
        try {
            $newbuilding = (new Newbuilding())->fill($newbuildingData);
            $newbuilding->save();

            $advantages = !empty($newbuildingData['advantages']) ? $newbuildingData['advantages'] : [];

            foreach($advantages as $advantageId) {
                if($advantage = Advantage::findOne($advantageId) === null) {
                    throw new NotFoundHttpException('Данные отсутсвуют');
                }
                $newbuilding->link('advantages', $advantage);
            }

            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $newbuilding;
    }
    
    /**
     * Edit newbuilding entities
     * 
     * @param array $newbuildingData
     * @throws \Exception
     */
    public function edit($newbuildingData)
    {
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            $this->fill($newbuildingData);
            $this->save();

            $advantages = ArrayHelper::getColumn($this->getAdvantages()->asArray()->all(), 'id');
            $receivedAdvantages = is_array($newbuildingData['advantages']) ? $newbuildingData['advantages'] : [];

            $addedAdvantages = Advantage::findAll(array_diff($receivedAdvantages, $advantages));
            foreach ($addedAdvantages as $advantage) {
                $this->link('advantages', $advantage);
            }

            $removedAdvantages = Advantage::findAll(array_diff($advantages, $receivedAdvantages));
            foreach ($removedAdvantages as $advantage) {
                $this->unlink('advantages', $advantage, true);
            }

            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            // echo '<pre>'; var_dump($e); die(); echo '</pre>';
            throw $e;
        }
    }
}
