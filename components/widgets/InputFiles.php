<?php

namespace app\components\widgets;

use yii\base\Widget;

/**
 * Widget for input files in model form window
 */
class InputFiles extends Widget
{    
    public $form;
    public $model;
    
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this->render('/widgets/input-files', [
            'form' => $this->form,
            'model' => $this->model,
        ]);
    }
}
