<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $developer->name;
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Застройщики', 'url' => ['admin/developer/index']];
$this->params['breadcrumbs'][] = ['label' => $developer->name, 'url' => ['admin/developer/update', 'id' => $developer->id]];
$this->params['breadcrumbs'][] = 'Офисы';
?>
<div class="office-index white-block">

    <?php if(\Yii::$app->user->can('admin')): ?>
        <p style="margin-bottom: 20px;">
            <?= Html::a('Добавить офис', 
                ['admin/office/developer-office/create', 'developerId' => $developer->id], 
                ['class' => 'btn btn-success']
            ) ?>
        </p>
    <?php endif; ?>
    
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
            [
                'attribute' => 'address',
                'enableSorting' => false
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'visibleButtons' => ['view' => false]
            ],
        ],
        'summary' => '',
        'emptyText' => 'Офисы ещё не добавлены',
    ]); ?>

    <?php Pjax::end(); ?>
</div>
