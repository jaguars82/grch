<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Тарифы';
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Банки', 'url' => ['admin/bank/index']];
$this->params['breadcrumbs'][] = ['label' => $bank->name, 'url' => ['admin/bank/update', 'id' => $bank->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bank-tariff-index white-block">

    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <p>
        <?= Html::a('Добавить тариф', ['create', 'bankId' => $bank->id], ['class' => 'btn btn-success']) ?>
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

            'id',
            [
                'attribute' => 'name',
                'enableSorting' => false,
                'content' => function ($model, $key, $index, $column) {
                    return  Html::a($model->name, ['update', 'id' => $model->id]);
                }
            ],
            'yearlyRateAsPercent',
            [
                'attribute' => 'InitialFeeRateAsPercent',
                'enableSorting' => false,
                'label' => 'Первоначальный взнос'
            ],
            'payment_period',

            ['class' => 'yii\grid\ActionColumn', 'visibleButtons' => ['view' => false]],
        ],
        'summary' => '',
        'emptyText' => 'Данные о банках отсутсвуют'
    ]); ?>
    
    <?php Pjax::end(); ?>
</div>
