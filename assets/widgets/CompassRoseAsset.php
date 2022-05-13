<?php

namespace app\assets\widgets;

use yii\web\AssetBundle;

/**
 * Asset bundle for user badge widget
 */
class CompassRoseAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/widgets/compass-rose.css'
    ];
    public $js = [
        'js/widgets/compass-rose.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
