<?php

namespace app\assets\widgets;

use yii\web\AssetBundle;

/**
 * Asset bundle for ImageView widget
 */
class GalleryAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/widgets/gallery.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
