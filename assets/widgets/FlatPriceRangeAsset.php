<?php

namespace app\assets\widgets;

use yii\web\AssetBundle;

/**
 * Asset bundle for flat-price-range widget
 */
class UserBadgeAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/widgets/flat-price-range.css'
    ];
    public $js = [
        'js/widgets/flat-price-range.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
