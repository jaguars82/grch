<?php

namespace app\assets\widgets;

use yii\web\AssetBundle;

/**
 * Asset bundle for InputData widget.
 */
class ImportDataAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/widgets/import-data.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
