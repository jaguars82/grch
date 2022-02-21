<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = $newbuildingComplex->name;
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Застройщики', 'url' => ['admin/developer/index']];
$this->params['breadcrumbs'][] = ['label' => $newbuildingComplex->developer->name, 'url' => ['admin/developer/update', 'id' => $newbuildingComplex->developer->id]];
$this->params['breadcrumbs'][] = ['label' => 'Жилые комплексы', 'url' => ['admin/newbuilding-complex/index',  'NewbuildingComplexSearch[developer_id]' => $newbuildingComplex->developer->id]];
$this->params['breadcrumbs'][] = ['label' => $newbuildingComplex->name, 'url' => ['admin/newbuilding-complex/update', 'id' => $newbuildingComplex->id]];
$this->params['breadcrumbs'][] = 'Отделки';
?>
<div class="white-block">
    <?php if(\Yii::$app->user->can('admin')): ?>
        <p style="margin-bottom: 20px;">
            <?= Html::a('Добавить отделку', 
                ['create', 'newbuildingComplexId' => $newbuildingComplex->id],
                ['class' => 'btn btn-success']) 
            ?>
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
            ['class' => 'yii\grid\SerialColumn', 'header' => ''],
            [
                'attribute' => 'name',
                'enableSorting' => true,
                'content' => function ($model, $key, $index, $column) {
                    return  Html::a($model->name, ['update', 'id' => $model->id]);
                }
            ],
            [
                'attribute' => 'detail',
                'enableSorting' => false
            ],
            ['class' => 'yii\grid\ActionColumn', 'visibleButtons' => ['view' => false]],
        ],
        'summary' => '',
        'emptyText' => 'Данные об отделках отсутсвуют'
    ]); ?>

    <?php Pjax::end(); ?>
</div>