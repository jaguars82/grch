<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Города';
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="city-index white-block">
    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <p style="margin-top: 20px;">
        <?= Html::a('Добавить город', ['create'], ['class' => 'btn btn-success']) ?>
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
            [
                'attribute' => 'region_district_id',
                'label' => 'Район',
                'enableSorting' => true,
                'content' => function ($model, $key, $index, $column) {
                    return isset($model->regiondistrict->name) ? $model->regiondistrict->name : '';
                }
            ],

            ['class' => 'yii\grid\ActionColumn', 'visibleButtons' => ['view' => false]],
        ],
        'summary' => '',
        'emptyText' => 'Города ещё не добавлены',
    ]); ?>

    <?php Pjax::end(); ?>
</div>
