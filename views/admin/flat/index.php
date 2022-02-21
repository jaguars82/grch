<?php
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $this yii\web\View */

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

$this->title = 'Квартиры';

$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Застройщики', 'url' => ['admin/developer/index']];
$this->params['breadcrumbs'][] = ['label' => $newbuilding->developer->name, 'url' => ['admin/developer/update', 'id' => $newbuilding->developer->id]];
$this->params['breadcrumbs'][] = ['label' => 'Жилые комплексы', 'url' => ['admin/newbuilding-complex/index',  'NewbuildingComplexSearch[developer_id]' => $newbuilding->developer->id]];
$this->params['breadcrumbs'][] = ['label' => $newbuilding->newbuildingComplex->name, 'url' => ['admin/newbuilding-complex/update', 'id' => $newbuilding->newbuildingComplex->id]];
$this->params['breadcrumbs'][] = ['label' => 'Позиции', 'url' => ['admin/newbuilding/index', 'newbuildingComplexId' => $newbuilding->newbuildingComplex->id]];
$this->params['breadcrumbs'][] = ['label' => $newbuilding->name, 'url' => ['admin/newbuilding/update', 'id' => $newbuilding->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="flat-index white-block">
    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <div style="margin: 20px 0;">
        <?= Html::a('Добавить квартиру', ['create', 'newbuildingId' => $newbuilding->id], ['class' => 'btn btn-success']) ?>
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

            'id',
            [
                'attribute' => 'number',
                'label' => 'Номер'
            ],
            [
                'attribute' => 'address',
                'label' => 'Адрес'
            ],
            'area',
            'rooms',
            'floor',
            'price_cash',
            'unit_price_cash',

            ['class' => 'yii\grid\ActionColumn', 'visibleButtons' => ['view' => false]],
        ],
        'summary' => '',
        'emptyText' => 'Данные о квартирах отсутствуют'
    ]); ?>
    
    <?php Pjax::end(); ?>
</div>
