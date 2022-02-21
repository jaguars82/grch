<?php

namespace app\assets\widgets;

use yii\web\AssetBundle;

/**
 * Asset bundle for ImageView widget
 */
class InputFilesAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/widgets/input-files.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
