<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Asset bundle for flat view page.
 */
class FlatViewAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/flat-view.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
