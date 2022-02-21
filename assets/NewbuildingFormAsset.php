<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Asset bundle for newbuilding form page.
 */
class NewbuildingFormAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/newbuilding-form.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
