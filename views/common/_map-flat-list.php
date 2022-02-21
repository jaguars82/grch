<?php

use yii\helpers\Html;
use yii\helpers\Url;

$format = \Yii::$app->formatter;
?>
<div class="flat-list-map--inner">
    <?php foreach($flats as $flat): ?>
        <div class="flat-list-map--item">
            <div class="image">
                <a href="<?= Url::to(['/flat/view', 'id' => $flat->id]) ?>">
                    <?php if(!is_null($flat->layout)): ?>
                        <?= Html::img(["/uploads/{$flat->layout}"]) ?>
                    <?php else: ?>
                        <?= Html::img([Yii::getAlias("@web/img/flat.png")]) ?>
                    <?php endif ?>
                </a>
            </div>
            <div class="content">
                <p class="title">
                    <?= Html::a($flat->newbuildingComplex->name, ['newbuilding-complex/view', 'id' => $flat->newbuildingComplex->id]) ?>
                </p>
                <div class="flex-row info">
                    <span class="price">
                        <?= $format->asCurrency($flat->price_cash); ?>
                    </span>
                    <span class="deadline">
                        Сдача: <?= is_null($flat->newbuilding->deadline) ? 'Нет данных' : $format->asQuarterAndYearDate($flat->newbuilding->deadline, false) ?>
                    </span>
                </div>
                <div class="flex-row params">
                    <span><?= $flat->roomsTitle ?></span>
                    <span><?= $format->asArea($flat->area) ?></span>
                    <span><?= $format->asFloor($flat->floor, $flat->newbuilding->total_floor) ?></span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>