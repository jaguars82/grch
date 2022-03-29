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
        // 'https://projects.davidlynch.org/maphilight/jquery.maphilight.min.js',
        // 'js/offer-make.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
