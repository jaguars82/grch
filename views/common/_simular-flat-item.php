<?php
/* @var $model app\models\Flat */

use yii\helpers\Html;

$format = \Yii::$app->formatter;
?>
<div class="similar-flat--item">
    <div class="similar-flat--item__image">
        <?php if(!is_null($model->layout)): ?>
            <?= Html::img(["/uploads/{$model->layout}"], ['class' => 'photo']) ?>
        <?php else: ?>
            <?= Html::img([Yii::getAlias("@web/img/flat.png")]) ?>
        <?php endif ?>
    </div>
    <div class="similar-flat--item__content">
        <?php if (!$model->isSold() && (\Yii::$app->user->can('admin') || \Yii::$app->user->can('agent'))): ?>
            <span class="favorite btn-favorite
                <?php if($model->isFavorite()): ?>
                    in
                <?php endif; ?>" data-flat-id="<?= $model->id ?>"></span>
        <?php endif; ?>

        <div class="flex-row info">
            <span class="price">
                <?= $format->asCurrency($model->price_cash); ?>
            </span>
            <span class="deadline">
                Сдача: <?= is_null($model->newbuilding->deadline) ? 'Нет данных' : $format->asQuarterAndYearDate($model->newbuilding->deadline, false) ?>
            </span>
        </div>
        <p class="area"><?= $format->asPricePerArea($model->pricePerArea) ?></p>
        <p class="title">
            <?= Html::a($model->newbuildingComplex->name, ['newbuilding-complex/view', 'id' => $model->newbuildingComplex->id]) ?>
        </p>
        <div class="flex-row params">
            <span><?= $model->roomsTitle ?></span> <span><?= $format->asArea($model->area) ?></span>
            <span><?= $format->asFloor($model->floor, $model->newbuilding->total_floor) ?> этаж</span>
        </div>
        <div class="flex-row align-center developer-info">
            <div class="image">
                <?php if(!is_null($model->newbuildingComplex->logo)): ?>
                    <?= Html::img([Yii::getAlias("@web/uploads/{$model->newbuildingComplex->logo}")]) ?>
                <?php else: ?>
                    <?= Html::img([Yii::getAlias("@web/img/newbuilding-complex.png")]) ?>
                <?php endif ?>
            </div>
            <p class="address">
                <?= is_null($model->newbuildingComplex->address) ? : $model->newbuildingComplex->address ?>
            </p>
        </div>
        <?= Html::a('Подробнее', ['flat/view', 'id' => $model->id], [
            'class' => 'btn btn-primary'
        ]) ?>

        <?php if($model->hasDiscount()): ?>
            <div class="btn btn-red">
                Действует скидка - <?= $format->asPercent($model->discount) ?>
            </div>
        <?php endif; ?>
    </div>
</div>