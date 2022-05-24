<?php

namespace app\assets\viewElements;

use yii\web\AssetBundle;

/**
 * Asset bundle for user badge widget
 */
class AccreditationAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/view-elements/accreditation.css'
    ];
    public $js = [
        'js/view-elements/accreditation.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}