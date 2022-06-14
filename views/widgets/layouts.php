<?php

use yii\helpers\Html;
use app\assets\widgets\LayoutsAsset;

LayoutsAsset::register($this);

?>
<?= $this->render('/widgets/modal-window') ?>
<div style="display: flex;">
    <div id="placeholder">
        <div id="area0" class="layout" style="width: 100%; max-width: 100%; height: 100%; max-height: 100%;">
            <div class="layout-buttons"><span id="layout-expand" class="material-icons-outlined zoom-button">zoom_in</span></div>
            <?= Html::img(["/uploads/{$flat->layout}"], ['style' => 'display: block; max-height: 100%; margin: 0 auto;']) ?>
            <div id="layout-modal" class="modal-window" data-idprefix="layout">
                <div class="modal-buttons"><span id="layout-close" class="material-icons-outlined">close</span></div>
                <div style="position: relative;">
                    <?= $this->render('/widgets/compass-rose', [
                        'id' => 'compass-rose-flat',
                        'azimuth' => $flat->entrance->azimuth
                    ]) ?>
                </div>
                <div class="modal-media-container">
                    <?= Html::img(["/uploads/{$flat->layout}"], ['id' => 'flat-layout']) ?>
                </div>
            </div>
        </div>
        <div id="area1" class="layout" style="display: none; width: 100%; height: 100%; max-height: 100%;">
            <div class="layout-buttons"><span id="entrance-expand" class="material-icons-outlined zoom-button">zoom_in</span></div>
            <div style="width: 100%; max-width: 100%; height: 100%; max-height: 100%;">
                <?= $floorLayoutImage ?>
            </div>
            <div id="entrance-modal" class="modal-window" data-idprefix="entrance">
                <div class="modal-buttons"><span id="entrance-close" class="material-icons-outlined">close</span></div>
                <div style="position: relative;">
                        <?= $this->render('/widgets/compass-rose', [
                        'id' => 'compass-rose-entrance',
                        'azimuth' => $flat->entrance->azimuth
                    ]) ?>
                </div>
                <div class="modal-media-container">
                    <div id="entrance-layout" style="width: 60%; max-width: 60%;">
                        <?= $floorLayoutImage ?>
                    </div>
                </div>
            </div>
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