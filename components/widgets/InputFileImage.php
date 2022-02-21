<?php

namespace app\components\widgets;

use yii\base\Widget;

/**
 * Widget for view image in model window
 */
class InputFileImage extends Widget
{    
    public $form;
    public $model;
    public $attribute;
    public $imageHeight;
    public $imageWidth;
    public $imageResetAttribute = NULL;
    
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this->render('/widgets/input-file-image', [
            'form' => $this->form,
            'model' => $this->model,
            'attribute' => $this->attribute,
            'imageHeight' => $this->imageHeight,
            'imageWidth' => $this->imageWidth,
            'imageResetAttribute' => $this->imageResetAttribute,
        ]);
    }
}
