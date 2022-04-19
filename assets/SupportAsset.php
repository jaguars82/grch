<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Asset bundle for user Profile
 */
class SupportAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/support.css'
    ];
    public $js = [
        'js/support.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
