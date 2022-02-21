<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Помощь';
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="faq-index white-block">

    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <p style="margin-top: 20px;">
        <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin([
        'id' => 'search-result',
        'enablePushState' => true,
        'enableReplaceState' => false,
    ]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'name',
                'enableSorting' => false,
                'content' => function ($model, $key, $index, $column) {
                    return  Html::a($model->name, ['update', 'id' => $model->id]);
                }
            ],
            ['class' => 'yii\grid\ActionColumn', 'visibleButtons' => ['view' => false]],
        ],
        'summary' => '',
        'emptyText' => 'Записи ещё не добавлены',
    ]); ?>

    <?php Pjax::end(); ?>
</div>
