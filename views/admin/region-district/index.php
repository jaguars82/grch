<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Административные районы';
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="region_district-index white-block">
    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <p style="margin-top: 20px;">
        <?= Html::a('Добавить административный район', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin([
        'id' => 'search-result',
        'enablePushState' => true,
        'enableReplaceState' => false,
    ]); ?>

    <?= $this->render('_search', [
        'model' => $searchModel,
        'dataProvider' => $dataProvider,
        'regions' => $regions,
    ]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            [
                'attribute' => 'region_id',
                'label' => 'Регион',
                'enableSorting' => true,
                'content' => function ($model, $key, $index, $column) {
                    return $model->region->name;
                }
            ],

            ['class' => 'yii\grid\ActionColumn', 'visibleButtons' => ['view' => false]],
        ],
        'summary' => '',
        'emptyText' => 'Административные районы ещё не добавлены',
    ]); ?>

    <?php Pjax::end(); ?>
</div>