<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Документы для ' . $newbuildingComplex->name;

$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Застройщики', 'url' => ['admin/developer/index']];
$this->params['breadcrumbs'][] = ['label' => $newbuildingComplex->developer->name, 'url' => ['admin/developer/update', 'id' => $newbuildingComplex->developer->id]];
$this->params['breadcrumbs'][] = ['label' => 'Жилые комплексы', 'url' => ['admin/newbuilding-complex/index']];
$this->params['breadcrumbs'][] = ['label' => $newbuildingComplex->name, 'url' => ['admin/newbuilding-complex/update', 'id' => $newbuildingComplex->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-index white-block">

    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <p>
        <?= Html::a('Добавить документ', ['create', 'newbuildingComplexId' => $newbuildingComplex->id], ['class' => 'btn btn-success']) ?>
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
            'file',
            'name',
            'size',
            'created_at',
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn', 'visibleButtons' => ['view' => false]],
        ],
        'summary' => '',
        'emptyText' => 'Документы ещё не добавлены',
    ]); ?>

    <?php Pjax::end(); ?>

</div>
