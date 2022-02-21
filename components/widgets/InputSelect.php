<?php

namespace app\components\widgets;

use yii\base\Widget;

/**
 * Widget for select item/items from array
 */
class InputSelect extends Widget
{
    public $array;
    public $checkedArray;
    public $field;
    public $displayField;
    public $label;
    public $size = 5;
    public $id;
    public $isMultiple = true;
    public $itemDataField;
    public $itemDataValue;
    public $defaultValue;
    
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if(is_array($this->defaultValue) && (empty($this->checkedArray) || empty($this->checkedArray[0]))) {
            $this->checkedArray = $this->defaultValue;
        }
        
        return $this->render('/widgets/input-select', [
            'array' => $this->array,
            'checkedArray' => $this->checkedArray,
            'field' => $this->field,
            'displayField' => $this->displayField,
            'label' => $this->label,
            'size' => $this->size,
            'id' => $this->id,
            'isMultiple' => $this->isMultiple,
            'itemDataField' => $this->itemDataField,
            'itemDataValue' => $this->itemDataValue,
            'defaultValue' => $this->defaultValue,
        ]);
    }
}
