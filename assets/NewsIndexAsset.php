<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Asset bundle for news index page.
 */
class NewsIndexAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/news-index.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
