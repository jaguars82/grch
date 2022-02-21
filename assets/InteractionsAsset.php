<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Asset bundle for interactions page.
 */
class InteractionsAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/interactions.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
