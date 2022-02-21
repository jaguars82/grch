<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 */
class NewAsset extends AssetBundle 
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap',
        'vendor/swiper/swiper-bundle.min.css',
        'vendor/select2/select2.min.css',
        'vendor/bootstrap-3.4.1/css/bootstrap.min.css',
        'vendor/scrollbar/simplebar.min.css',
        'vendor/fancybox/jquery.fancybox.min.css',
        'css/style.css',
    ];
    public $js = [
        'vendor/swiper/swiper-bundle.min.js',
        'vendor/select2/select2.full.min.js',
        'vendor/select2/ru.js',
        'vendor/bootstrap-3.4.1/js/bootstrap.min.js',
        'vendor/stycky-polyfill/stickyfill.min.js',
        'vendor/scrollbar/simplebar.min.js',
        'vendor/match-height/jquery.matchHeight-min.js',
        'vendor/fancybox/jquery.fancybox.min.js',
        'js/common.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}