<?php
use yii\helpers\Html;
use yii\helpers\Url;

$format = \Yii::$app->formatter;
?>

<div class="advantages">
    <p class="title">
        Преимущества
    </p>
    <?php foreach($advantages as $advantage): ?>
        <?php if(!is_null($advantage->icon)): ?>
            <div class="advantages--item">
                <span class="icon">
                    <?= Html::img(Yii::getAlias("@web/uploads/$advantage->icon")) ?>
                </span>
                <?= $advantage->name ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>