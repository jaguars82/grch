<?php

namespace app\assets\widgets;

use yii\web\AssetBundle;

/**
 * Asset bundle for ImageView widget
 */
class FileInputButtonAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/widgets/file-input-button.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
