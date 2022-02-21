<?php

namespace app\assets\widgets;

use yii\web\AssetBundle;

/**
 * Asset bundle for ImageView widget
 */
class FloorLayoutAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'https://projects.davidlynch.org/maphilight/jquery.maphilight.min.js',
        'js/widgets/floor-layout.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
