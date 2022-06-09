<?php

use yii\helpers\Html;
use app\assets\widgets\LayoutsAsset;

LayoutsAsset::register($this);

?>

<div style="display: flex;">
<div id="placeholder">
    <div id="area0" class="layout" style="width: 100%; height: 100%; max-height: 100%;">
        <div style="position: relative;">
            <?= $this->render('/widgets/compass-rose', [
                'id' => 'compass-rose-flat',
                'azimuth' => $flat->entrance->azimuth
            ]) ?>
        </div>
        <?= Html::img(["/uploads/{$flat->layout}"], ['id' => 'flat-layout', 'style' => 'display: block; max-height: 100%; margin: 0 auto;']) ?>
    </div>
    <div id="area1" class="layout" style="display: none; width: 100%; height: 100%; max-height: 100%;">
        <div style="position: relative;">
                <?= $this->render('/widgets/compass-rose', [
                'id' => 'compass-rose-entrance',
                'azimuth' => $flat->entrance->azimuth
            ]) ?>
        </div>
        <div id="entrance-layout" style="width: 400px; max-width: 400px; display: block; margin: 50px 50px;">
            <?= $floorLayoutImage ?>
        </div>
    </div>
    <div id="area2" class="layout" style="display: none; width: 100%; height: 100%; max-height: 100%;">
        <!--<img src="https://dbldom.ru/wp-content/uploads/2021/10/12b2.jpg" />-->
        <img src="/uploads/<?= $flat->newbuildingComplex->master_plan ?>" style="display: block; max-height: 100%; margin: 0 auto;" />
    </div>
</div>
<ul id="thumbs">
    <li>
        <a id="thumb1" data-index="0" href="#">
        <?= Html::img(["/uploads/{$flat->layout}"], ['id' => 'flat-layout-thumb', 'style' => 'display: block; max-height: 100%; margin: 0 auto;']) ?>
        </a>
    </li>
    <li>
        <a id="thumb2" data-index="1" href="#">
            <div>
                <?= $floorLayoutImage ?>
            </div>
        </a>
    </li>
    <li>
        <a id="thumb3" data-index="2" href="#">
            <img src="/uploads/<?= $flat->newbuildingComplex->master_plan ?>" style="display: block; max-height: 100%; margin: 0 auto;" />
        </a>
    </li>
</ul>
</div>
