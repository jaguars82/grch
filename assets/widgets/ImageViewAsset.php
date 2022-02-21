<?php

namespace app\assets\widgets;

use yii\web\AssetBundle;

/**
 * Asset bundle for ImageView widget
 */
class ImageViewAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'https://jqueryrotate.com/jQueryRotateCompressed.js',
        'js/widgets/image-view.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
