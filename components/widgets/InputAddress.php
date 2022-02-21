<?php

namespace app\components\widgets;

use yii\base\Widget;

/**
 * Widget for view image in model window
 */
class InputAddress extends Widget
{
    public $form;
    public $model;
    public $attribute;
    
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this->render('/widgets/input-address', [
            'form' => $this->form,
            'model' => $this->model,
            'attribute' => $this->attribute
        ]);
    }
}
