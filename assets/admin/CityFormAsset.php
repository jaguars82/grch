<?php

namespace app\assets\admin;

use yii\web\AssetBundle;

/**
 * Asset bundle for user badge widget
 */
class CityFormAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        //'css/admin/city-form.css'
    ];
    public $js = [
        'js/admin/city-form.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
