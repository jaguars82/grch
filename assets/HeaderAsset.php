<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Asset bundle for Header
 */
class HeaderAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/header.css'
    ];
    public $js = [
        'js/header.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
