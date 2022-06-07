<?php

namespace app\assets\widgets;

use yii\web\AssetBundle;

/**
 * Asset bundle for user badge widget
 */
class ModalWindowAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/widgets/modal-window.css'
    ];
    public $js = [
        'js/widgets/modal-window.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
