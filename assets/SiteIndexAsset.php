<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Asset bundle for news index page.
 */
class SiteIndexAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/site-index.js',
    ];
    public $depends = [
        //'yii\web\JqueryAsset',
        'yii\jui\JuiAsset',
    ];
}
