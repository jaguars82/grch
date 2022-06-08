<?php

namespace app\assets\widgets\controls;

use yii\web\AssetBundle;

/**
 * Asset bundle for user badge widget
 */
class ButtonAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/widgets/controls/button.css'
    ];
    public $js = [
        'js/widgets/controls/button.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
