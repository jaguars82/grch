<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Asset bundle for flat form page.
 */
class DeveloperFormAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/developer-form.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
