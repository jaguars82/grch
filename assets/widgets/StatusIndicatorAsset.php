<?php

namespace app\assets\widgets;

use yii\web\AssetBundle;

/**
 * Asset bundle for user badge widget
 */
class StatusIndicatorAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/widgets/status-indicator.css'
    ];
    public $js = [
        'js/widgets/status-indicator.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
