<?php

namespace app\components\validators;

use app\models\FloorLayout;
use yii\validators\Validator;

/**
 * Implements validation for flat validation
 */
class FloorAndSectionValidator extends Validator
{    
    /**
     * Execute validation
     * 
     * @param type $model
     * @return boolean
     */
    public static function execute($floor_layout_id, $newbuilding_id, $modelFloor, $modelSection)
    {
        $floorLayouts = FloorLayout::find()->where(['newbuilding_id' => $newbuilding_id])->all();
        
        foreach ($floorLayouts as $floorLayout) {
            if ($floor_layout_id == $floorLayout->id) {
                continue;
            }
            
            $isSingleFloorLayout = count($floorsLayout = explode('-', $floorLayout->floor)) < 2;
            $isSingleSectionLayout = count($sectionsLayout = explode('-', $floorLayout->section)) < 2;
            $isSingleFloorModel = count($floorsModel = explode('-', $modelFloor)) < 2;
            $isSingleSectionModel = count($sectionsModel = explode('-', $modelSection)) < 2;
            
            $isFloorsIntersect = ($isSingleFloorLayout && $isSingleFloorModel && $modelFloor == $floorLayout->floor)    
                || (!$isSingleFloorLayout && !$isSingleFloorModel && $floorsModel[0] >= $floorsLayout[0] && $floorsModel[0] <= $floorsLayout[1])
                || (!$isSingleFloorLayout && !$isSingleFloorModel && $floorsLayout[0] >= $floorsModel[0] && $floorsLayout[0] <= $floorsModel[1]);
            
            $isSectionsIntersect = ($isSingleSectionLayout && $isSingleSectionModel && $modelSection == $floorLayout->section)    
                || (!$isSingleSectionLayout && !$isSingleSectionModel && $sectionsModel[0] >= $sectionsLayout[0] && $sectionsModel[0] <= $sectionsLayout[1])
                || (!$isSingleSectionLayout && !$isSingleSectionModel && $sectionsLayout[0] >= $sectionsModel[0] && $sectionsLayout[0] <= $sectionsModel[1]);
            
            if ($isFloorsIntersect && $isSectionsIntersect) {
                return 'Уже есть планировка с данными значениями этажей и подъездов';
            }
        }
        
        return false;
    }
    
    public function validateAttribute($model, $attribute)
    {        
        if (($result = self::execute($model->floorLayoutId, $model->newbuilding_id, $model->floor, $model->section))) {
            $this->addError($model, $attribute, $result);
        }
    }
    
    public function clientValidateAttribute($model, $attribute, $view)
    {
        return <<<JS
floor_layout_id = $('#floor_layout_id').val();
newbuilding_id = $('input[name="FloorLayoutForm[newbuilding_id]"]').val();
model_floor = $('input[name="FloorLayoutForm[floor]"]').val();
model_section = $('input[name="FloorLayoutForm[section]"]').val();      

deferred.push($.post('/floor-layout/check', {floor_layout_id: floor_layout_id, newbuilding_id: newbuilding_id, model_floor: model_floor, model_section: model_section})
    .fail(function(data) {
        messages.push(data.responseJSON.message);
    }))
JS;
    }
}
