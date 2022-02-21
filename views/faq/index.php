<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Помощь';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="faq-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if(\Yii::$app->user->can('admin')): ?>
    <p style="margin: 20px 0;">
        <?= Html::a('Добавить', ['admin/faq/create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php endif ?>

    <div class="faq-items">
        <?= ListView::widget([
            'dataProvider' => $dataProvider,
            'itemView' => '_item',
            'summary' => '',
            'emptyText' => 'Данные отсутсвуют'
        ]); ?>
    </div>
</div>
