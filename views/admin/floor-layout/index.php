<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Планировки этажей';

$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Застройщики', 'url' => ['admin/developer/index']];
$this->params['breadcrumbs'][] = ['label' => $newbuilding->developer->name, 'url' => ['admin/developer/update', 'id' => $newbuilding->developer->id]];
$this->params['breadcrumbs'][] = ['label' => 'Жилые комплексы', 'url' => ['admin/newbuilding-complex/index',  'NewbuildingComplexSearch[developer_id]' => $newbuilding->developer->id]];
$this->params['breadcrumbs'][] = ['label' => $newbuilding->newbuildingComplex->name, 'url' => ['admin/newbuilding-complex/update', 'id' => $newbuilding->newbuildingComplex->id]];
$this->params['breadcrumbs'][] = ['label' => 'Позиции', 'url' => ['admin/newbuilding/index', 'newbuildingComplexId' => $newbuilding->newbuildingComplex->id]];
$this->params['breadcrumbs'][] = ['label' => $newbuilding->name, 'url' => ['admin/newbuilding/update', 'id' => $newbuilding->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="floor-layout-index white-block">

    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <div style="margin: 20px 0;">
        <?= Html::a('Добавить планировку этажа', ['create', 'newbuildingId' => $newbuilding->id], ['class' => 'btn btn-success']) ?>
    </div>

    <?php Pjax::begin([
        'id' => 'search-result',
        'enablePushState' => true,
        'enableReplaceState' => false,
    ]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'floor',
            'section',
            //'map:ntext',

            ['class' => 'yii\grid\ActionColumn', 'visibleButtons' => ['view' => false]],
        ],
        'summary' => '',
        'emptyText' => 'Данные о планировках отсутствуют'
    ]); ?>

    <?php Pjax::end(); ?>
</div>
