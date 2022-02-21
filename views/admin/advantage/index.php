<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Преимущества';
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="advantage-index white-block">

    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <?php if(\Yii::$app->user->can('admin')): ?>
    <p style="margin-top: 20px;">
        <?= Html::a('Добавить преимущество', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php endif ?>

    <?php Pjax::begin([
        'id' => 'search-result',
        'enablePushState' => true,
        'enableReplaceState' => false,
    ]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',

            ['class' => 'yii\grid\ActionColumn', 'visibleButtons' => ['view' => false]],
        ],
        'summary' => '',
        'emptyText' => 'Преимущества ещё не добавлены',
    ]); ?>

    <?php Pjax::end(); ?>
</div>
