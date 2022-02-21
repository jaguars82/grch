<?php

namespace app\components\widgets;

use yii\base\Widget;

/**
 * Widget for select and load file
 */
class FileInputButton extends Widget
{
    public $url;
    public $name;
    public $model;
    public $field;
    public $container;
    public $reinit = true;
    
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this->render('/widgets/file-input-button', [
            'url' => $this->url,
            'name' => $this->name,
            'model' => $this->model,
            'field' => $this->field,
            'container' => $this->container,
            'reinit' => $this->reinit,
        ]);
    }
}
