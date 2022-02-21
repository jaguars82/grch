<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Asset bundle for news index page.
 */
class OfferIndexAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/offer-index.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
