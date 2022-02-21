<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\search\DistrictSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Районы';
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="district-index white-block">

    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <p style="margin-top: 20px;">
        <?= Html::a('Добавить район', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin([
        'id' => 'search-result',
        'enablePushState' => true,
        'enableReplaceState' => false,
    ]); ?>

    <?= $this->render('_search', [
        'model' => $searchModel,
        'dataProvider' => $dataProvider,
        'cities' => $cities,
    ]); ?>

    <div class="data-wrap">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'id',
                [
                    'attribute' => 'name',
                    'enableSorting' => false,
                ],
                [
                    'attribute' => 'city_id',
                    'label' => 'Город',
                    'enableSorting' => false,
                    'content' => function ($model, $key, $index, $column) {
                        return $model->city->name;
                    }
                ],
                ['class' => 'yii\grid\ActionColumn', 'visibleButtons' => ['view' => false]],
            ],
            'summary' => '',
            'emptyText' => 'Районы ещё не добавлены',
        ]); ?>
    </div>

    <?php Pjax::end(); ?>
</div>
