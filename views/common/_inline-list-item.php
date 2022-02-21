<?php
use yii\helpers\Html;

$format = \Yii::$app->formatter;
?>

<div class="inline-list--item">
    <div class="inline-list--item__top">
        <?php if($displayType == true): ?>
            <span class="type <?= $model->isAction() ? 'stock' : 'news' ?>">
                <?= $model->isAction() ? 'Акция' : 'Новость' ?>
            </span>
        <?php endif; ?>
        <span class="date">
            <?= $format->asDate($model->created_at, 'php:d.m.Y') ?>
        </span>
        <p class="title">
            <?= $model->title ?>
        </p>
    </div>
    <div class="inline-list--item__bottom">
        <p class="desc">
            <?= $format->asShortText($model->detail, 250, true) ?>
        </p>
        <?= Html::a('Подробнее', ['news/view', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </div>
</div>