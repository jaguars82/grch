<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Asset bundle for ImageView widget
 */
class OfferMakeAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/offer.css'
    ];
    public $js = [
        'https://projects.davidlynch.org/maphilight/jquery.maphilight.min.js',
        'js/offer-make.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
