<?php

namespace app\models\service;

use app\components\exceptions\AppException;
use app\models\Bank;
use app\models\Advantage;
use app\models\Image;
use app\models\NewbuildingComplexStage;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for processing newbuilding complexes.
 */
class NewbuildingComplex extends \app\models\NewbuildingComplex
{    
    /**
     * Create newbuilding complex's entities
     * 
     * @param array $newbuildingComplexData
     * @return \app\models\service\NewbuildingComplex
     * @throws \Exception
     * @throws NotFoundHttpException
     */
    public static function create($newbuildingComplexData)
    {
        $transaction = \Yii::$app->db->beginTransaction();
            
        try {
            $newbuildingComplex = (new NewbuildingComplex())->fill($newbuildingComplexData);
            $newbuildingComplex->save();
            
            $advantages = !empty($newbuildingComplexData['advantages']) ? $newbuildingComplexData['advantages'] : [];

            foreach($advantages as $advantageId) {
                if(($advantage = Advantage::findOne($advantageId)) === null) {
                    throw new NotFoundHttpException('Данные отсутсвуют');
                }
                $newbuildingComplex->link('advantages', $advantage);
            }

            $images = is_array($newbuildingComplexData['images']) ? $newbuildingComplexData['images'] : [];

            foreach ($images as $image) {
                $model = new Image();
                $model->file = $image;
                if($model->save()) {
                    $newbuildingComplex->link('images', $model);
                }
            }

            $stages = isset($newbuildingComplexData['stages']) && !empty($newbuildingComplexData['stages']) ? $newbuildingComplexData['stages'] : [];

            foreach($stages as $stage) {
                $model = new NewbuildingComplexStage();
                $model->newbuilding_complex_id = $newbuildingComplex->id;
                $model->name = $stage['name'];
                $model->description = $stage['description'];

                $model->save();
            }
            
            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $newbuildingComplex;
    }
    
    /**
     * Edit newbuilding complex's entities
     * 
     * @param array $newbuildingComplexData
     * @throws \Exception
     */
    public function edit($newbuildingComplexData, $appendix = true)
    {
        if (isset($newbuildingComplexData['developer_id'])) {
            unset($newbuildingComplexData['developer_id']);
        }
      
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            $this->fill($newbuildingComplexData, ['logo', 'master_plan', 'project_declaration']);
            
            if ($newbuildingComplexData['is_logo_reset']) {
                $this->logo = NULL;
            } else {
                $this->logo = (!is_null($newbuildingComplexData['logo'])) ? $newbuildingComplexData['logo'] : $this->logo;
            }

            if ($newbuildingComplexData['is_master_plan_reset']) {
                $this->master_plan = NULL;
            } else {
                $this->master_plan = (!is_null($newbuildingComplexData['master_plan'])) ? $newbuildingComplexData['master_plan'] : $this->master_plan;
            }
            
            $this->save();

            if ($appendix === true) {

                $banks = ArrayHelper::getColumn($this->getBanks()->asArray()->all(), 'id');
                $receivedBanks = is_array($newbuildingComplexData['banks']) ? $newbuildingComplexData['banks'] : [];
                
                $addedBanks = Bank::findAll(array_diff($receivedBanks, $banks));
                foreach ($addedBanks as $bank) {
                    $this->link('banks', $bank);
                }

                $removedBanks = Bank::findAll(array_diff($banks, $receivedBanks));
                foreach ($removedBanks as $bank) {
                    $this->unlink('banks', $bank, true);
                }

                $advantages = ArrayHelper::getColumn($this->getAdvantages()->asArray()->all(), 'id');
                $receivedAdvantages = is_array($newbuildingComplexData['advantages']) ? $newbuildingComplexData['advantages'] : [];
                
                $addedAdvantages = Advantage::findAll(array_diff($receivedAdvantages, $advantages));
                foreach ($addedAdvantages as $advantage) {
                    $this->link('advantages', $advantage);
                }

                $removedAdvantages = Advantage::findAll(array_diff($advantages, $receivedAdvantages));
                foreach ($removedAdvantages as $advantage) {
                    $this->unlink('advantages', $advantage, true);
                }

                $savedImages = is_array($newbuildingComplexData['savedImages']) ? $newbuildingComplexData['savedImages'] : [];
                $addedImages = is_array($newbuildingComplexData['images']) ? $newbuildingComplexData['images'] : [];
                
                if(!is_null($this->images) && !empty($this->images)) {
                    foreach($this->images as $image) {
                        if(!in_array($image->id, $savedImages)) {
                            $this->unlink('images', $image, true);
                        }
                    }
                }

                foreach ($addedImages as $image) {
                    $model = new Image();
                    $model->file = $image;
                    if($model->save()) {
                        $this->link('images', $model);
                    }
                }

                $addedStages = is_array($newbuildingComplexData['stages']) ? $newbuildingComplexData['stages'] : [];

                $stagesIds = ArrayHelper::getColumn($this->getStages()->asArray()->all(), 'id');
                $addedStagesIds = ArrayHelper::getColumn($addedStages, 'id');
                
                $removedStages = NewbuildingComplexStage::findAll(array_diff($stagesIds, $addedStagesIds));

                foreach($removedStages as $stage) {
                    $stage->delete();
                }
                
                foreach($addedStages as $stage) {

                    if($stage['id']) {
                        $model = NewbuildingComplexStage::findOne($stage['id']);
                    } else {
                        $model = new NewbuildingComplexStage();
                        $model->newbuilding_complex_id = $this->id;
                    }

                    $model->name = $stage['name'];
                    $model->description = $stage['description'];

                    $model->save();
                }
            }

            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
    /**
     * Remove newbuilding complex's entities
     * 
     * @throws AppException
     */
    public function remove()
    {
        if (count($this->newbuildings)) {
            throw new AppException('Жилой комплекс не может быть удален, так как имеет этапы');
        }

        $this->delete();
    }
}
