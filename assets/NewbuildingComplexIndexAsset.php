<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Asset bundle for flat view page.
 */
class NewbuildingComplexIndexAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/newbuilding-complex-index.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
