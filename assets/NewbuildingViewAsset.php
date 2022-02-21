<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Asset bundle for flat view page.
 */
class NewbuildingViewAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/newbuilding-view.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
