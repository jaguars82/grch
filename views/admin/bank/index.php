<?php
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Банки';
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="bank-index white-block">
    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>
    
    <?php if(\Yii::$app->user->can('admin')): ?>
        <p><?= Html::a('Добавить банк', ['admin/bank/create'], ['class' => 'btn btn-success', 'style' => 'margin-bottom: 10px']) ?></p>
    <?php endif ?>
    
    <?= $this->render('_search', ['model' => $searchModel]) ?>   
    
    <?php Pjax::begin([
        'id' => 'search-result',
        'enablePushState' => true,
        'enableReplaceState' => false,
    ]); ?>
    
    <div id="data-wrap">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn', 'header' => ''],
                [
                    'attribute' => 'logo',
                    'content' => function ($model, $key, $index, $column) {
                        if(!is_null($model->logo)) {
                            return Html::img([Yii::getAlias("@web/uploads/{$model->logo}")], ['style' => 'max-width: 100%; max-height: 50px;']);
                        } else {
                            return Html::img([Yii::getAlias("@web/img/developer.png")], ['height' => 50]);
                        }
                    }
                ],
                [
                    'attribute' => 'name',
                    'enableSorting' => false,
                    'content' => function ($model, $key, $index, $column) {
                        return  Html::a($model->name, ['update', 'id' => $model->id]);
                    }
                ],
                ['class' => 'yii\grid\ActionColumn', 'visibleButtons' => ['view' => false]],
            ],
            'summary' => '',
            'emptyText' => 'Данные о банках отсутсвуют'
        ]);  ?>
    </div>
    
    <?php Pjax::end(); ?>
</div>
