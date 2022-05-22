<?php

namespace app\assets\widgets;

use yii\web\AssetBundle;

/**
 * Asset bundle for flats chess
 */
class FlatsChessAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/widgets/flats-chess.css'
    ];
    public $js = [
        'js/widgets/flats-chess.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
