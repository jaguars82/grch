<?php

namespace app\assets\widgets;

use yii\web\AssetBundle;

/**
 * Asset bundle for ImageView widget
 */
class InputAddressAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/widgets/input-address.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
    
    public function init()
    {
        parent::init();
        $this->js[] = 'https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey=' . \Yii::$app->params['yandexApiKey'];
    }
}
