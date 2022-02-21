<?php

namespace app\components\widgets;

use yii\base\Widget;

/**
 * Widget for view image in model window
 */
class ImageView extends Widget
{
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this->render('/widgets/image-view');
    }
}
