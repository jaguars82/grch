<?php
/* @var $this yii\web\View */

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

$this->title = 'Агентства недвижимости';
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = 'Агентства недвижимости';
?>
<div class="white-block">
    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <?php if(\Yii::$app->user->can('admin')): ?>
        <p><?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success', 'style' => 'margin-bottom: 10px']) ?></p>
    <?php endif ?>

    <?= $this->render('_search', ['model' => $searchModel]) ?>

    <?php Pjax::begin([
        'id' => 'search-result',
        'enablePushState' => true,
        'enableReplaceState' => false,
    ]); ?>
        
    <div id="search-result-count" style="margin-bottom: 15px">
        Найдено агенств недвижимости: <?= $itemsCount ?>
    </div>
        
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
                    'address',
                    ['class' => 'yii\grid\ActionColumn', 'visibleButtons' => ['view' => false]],
                ],
                'summary' => '',
                'emptyText' => 'Данные об агенствах недвижимости отсутсвуют'
            ]); ?>
    </div>
        
    <?php Pjax::end(); ?>
</div>