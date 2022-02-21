<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Asset bundle for news index page.
 */
class SiteSearchAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/site-search.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
