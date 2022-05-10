<?php

namespace app\assets\widgets;

use yii\web\AssetBundle;

/**
 * Asset bundle for user badge widget
 */
class IconedButtonAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/widgets/iconed-button.css'
    ];
    public $js = [
        'js/widgets/iconed-button.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
