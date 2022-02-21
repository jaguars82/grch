<?php

namespace app\components\widgets;

use yii\base\Widget;

/**
 * Widget for view image in model window
 */
class Gallery extends Widget
{
    public $images;
    public $fileField;
    public $colSizeClass;
    
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this->render('/widgets/gallery', [
            'images' => $this->images,
            'fileField' => !empty($this->fileField) ? $this->fileField : 'image'
        ]);
    }
}
