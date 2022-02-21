<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Asset bundle for flat view page.
 */
class DeveloperViewAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/developer-view.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
