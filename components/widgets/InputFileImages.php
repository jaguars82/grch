<?php

namespace app\components\widgets;

use yii\base\Widget;

/**
 * Widget for view image in model window
 */
class InputFileImages extends Widget
{    
    public $form;
    public $model;
    public $fileField;
    public $label;
    public $value;
    
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this->render('/widgets/input-file-images', [
            'form' => $this->form,
            'model' => $this->model,
            'fileField' => $this->fileField,
            'label' => $this->label,
            'value' => $this->value
        ]);
    }
}
