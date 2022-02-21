<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Asset bundle for newbuilding complex form.
 */
class NewbuildingComplexFormAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/newbuilding-complex-form.js',
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
