<?php

namespace app\components\widgets;

use yii\base\Widget;

/**
 * Widget for view image in model window
 */
class ImportData extends Widget
{
    public $form;
    public $model;
    public $values;
    
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this->render('/widgets/import-data', [
            'form' => $this->form,
            'model' => $this->model,
            'values' => $this->values,
        ]);
    }
}
