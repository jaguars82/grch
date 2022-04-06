<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Asset bundle for ImageView widget
 */
class ProfileAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/profile.css'
    ];
    public $js = [
        'js/profile.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
