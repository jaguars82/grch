<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
?>

<div class="container">
    <div class="not-fount-page">
        <?= Html::img(\Yii::getAlias('@web/img/icons/404.svg')) ?>
        <p class="h3">
            Страница не найдена
        </p>
        <p class="small">
            В адресе возможна ошибка или страница удалена
        </p>
        <?= Html::a('Перейти на Главную страницу', ['site/index'], ['class' => 'btn btn-primary']);?>
    </div>
</div>