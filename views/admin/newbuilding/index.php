<?php
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Позиции';
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Застройщики', 'url' => ['admin/developer/index']];
$this->params['breadcrumbs'][] = ['label' => $newbuildingComplex->developer->name, 'url' => ['admin/developer/update', 'id' => $newbuildingComplex->developer->id]];
$this->params['breadcrumbs'][] = ['label' => 'Жилые комплексы', 'url' => ['admin/newbuilding-complex/index',  'NewbuildingComplexSearch[developer_id]' => $newbuildingComplex->developer->id]];
$this->params['breadcrumbs'][] = ['label' => $newbuildingComplex->name, 'url' => ['admin/newbuilding-complex/update', 'id' => $newbuildingComplex->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="newbuilding-index white-block">
    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <p style="margin: 20px 0;">
        <?= Html::a('Добавить позицию', ['create', 'newbuildingComplexId' => $newbuildingComplex->id], ['class' => 'btn btn-success']) ?>
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
                'enableSorting' => true,
                'content' => function ($model, $key, $index, $column) {
                    return  Html::a($model->name, ['update', 'id' => $model->id]);
                }
            ],
            'address',
            'total_floor',

            ['class' => 'yii\grid\ActionColumn', 'visibleButtons' => ['view' => false]],
        ],
    ]); ?>

    <?php Pjax::end(); ?>
</div>
