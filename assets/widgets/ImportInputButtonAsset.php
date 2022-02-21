<?php

namespace app\assets\widgets;

use yii\web\AssetBundle;

/**
 * Asset bundle for ImageView widget
 */
class ImportInputButtonAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/widgets/import-input-button.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
