<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Asset bundle for Header
 */
class LoginAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/login.css'
    ];
    public $js = [
        'js/login.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
