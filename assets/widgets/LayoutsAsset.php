<?php

namespace app\assets\widgets;

use yii\web\AssetBundle;

/**
 * Asset bundle for user badge widget
 */
class LayoutsAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/widgets/layouts.css'
    ];
    public $js = [
        'js/widgets/layouts.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}