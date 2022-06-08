<?php

use yii\helpers\Html;
use app\assets\widgets\LayoutsAsset;

LayoutsAsset::register($this);

?>

<div id="placeholder">
    <div id="area0" style="width: 100%; height: 100%; max-height: 100%;">
        <div style="position: relative;">
            <?= $this->render('/widgets/compass-rose', [
                'id' => 'compass-rose-flat',
                'azimuth' => $flat->entrance->azimuth
            ]) ?>
        </div>
        <?= Html::img(["/uploads/{$flat->layout}"], ['id' => 'flat-layout', 'style' => 'display: block; max-height: 100%; margin: 0 auto;']) ?>
    </div>
    <div id="area1" style="display: none">
        <img src="https://fulcrum-spb.ru/upload/iblock/ee4/9nhenufxkersusgtei9kasg7hfa4b5y5.jpg" />
    </div>
    <div id="area2" style="display: none">
        <img src="https://dbldom.ru/wp-content/uploads/2021/10/12b2.jpg" />
    </div>
</div>
<ul id="thumbs">
    <li>
        <a id="thumb1" data-index="0" href="#">
        <?= Html::img(["/uploads/{$flat->layout}"], ['id' => 'flat-layout-thumb', 'style' => 'display: block; max-height: 100%; margin: 0 auto;']) ?>
        </a>
    </li>
    <li><a id="thumb2" data-index="1" href="#"><span>Home 2</span></a></li>
    <li><a id="thumb3" data-index="2" href="#"><span>Home 3</span></a></li>
</ul>
