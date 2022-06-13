<?php

use yii\helpers\Html;
use app\assets\widgets\LayoutsAsset;

LayoutsAsset::register($this);

?>

<div style="display: flex;">
    <div id="placeholder">
        <div id="area0" class="layout" style="width: 100%; height: 85%; max-height: 85%;">
            <div style="position: relative;">
                <?= $this->render('/widgets/compass-rose', [
                    'id' => 'compass-rose-flat',
                    'azimuth' => $flat->entrance->azimuth
                ]) ?>
            </div>
            <?= Html::img(["/uploads/{$flat->layout}"], ['id' => 'flat-layout', 'style' => 'display: block; max-height: 100%; margin: 0 auto;']) ?>
            <div id="layout-modal" class="modal-window" data-idprefix="layout">
                <div id="layout-close">Close</div>
                <?= Html::img(["/uploads/{$flat->layout}"], ['id' => 'flat-layout', 'style' => 'display: block; max-height: 100%; margin: 0 auto;']) ?>
            </div>
            <div id="layout-expand">Развернуть</div>
        </div>
        <div id="area1" class="layout" style="display: none; width: 100%; height: 100%; max-height: 100%;">
            <div style="position: relative;">
                    <?= $this->render('/widgets/compass-rose', [
                    'id' => 'compass-rose-entrance',
                    'azimuth' => $flat->entrance->azimuth
                ]) ?>
            </div>
            <div id="entrance-layout" style="width: 450px; max-width: 450px; height: 100%; max-height: 100%; display: block; margin: 20px 100px;">
                <?= $floorLayoutImage ?>
            </div>
            <div id="entrance-modal" class="modal-window" data-idprefix="entrance">
                <div id="entrance-close">Close</div>
                <?= $floorLayoutImage ?>
            </div>
            <div id="entrance-expand">Развернуть</div>
            <?= $this->render('/widgets/modal-window') ?>
        </div>
        <div id="area2" class="layout" style="display: none; width: 100%; height: 100%; max-height: 100%;">
            <!--<img src="https://dbldom.ru/wp-content/uploads/2021/10/12b2.jpg" />-->
            <img src="/uploads/<?= $flat->newbuildingComplex->master_plan ?>" style="display: block; max-height: 100%; margin: 0 auto;" />
        </div>
    </div>
    <ul id="thumbs">
        <li>
            <a id="thumb1" data-index="0" href="#" title="Квартира">
                <img src="/img/icons/layout-flat.svg" />
            </a>
        </li>
        <li>
            <a id="thumb2" data-index="1" href="#" title="Этаж">
            <img src="/img/icons/layout-floor.svg" />
            </a>
        </li>
        <li>
            <a id="thumb3" data-index="2" href="#"  title="Генплан">
                <img src="/img/icons/layout-genplan.svg" />
            </a>
        </li>
    </ul>
</div>