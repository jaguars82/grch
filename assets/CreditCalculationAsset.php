<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Asset bundle for credit calculation page.
 */
class CreditCalculationAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/credit-calculation.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
