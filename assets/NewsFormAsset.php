<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Asset bundle for news form.
 */
class NewsFormAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/news-form.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
