<?php

namespace app\assets\widgets;

use yii\web\AssetBundle;

/**
 * Asset bundle for user badge widget
 */
class UserBadgeAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/widgets/user-badge.css'
    ];
    public $js = [
        'js/widgwts/user-badge.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
