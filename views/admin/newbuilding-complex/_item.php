<?php
/* @var $model app\models\Developer */

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
?>

<div class="text-center">
    <h3>
        <a href="<?= Url::to(['admin/developer/update', 'id' => $model['id']]) ?>"><?= $model['name'] ?></a>
        <?= Html::a('Добавить ЖК', Url::to(['admin/newbuilding-complex/create', 'developerId' => $model['id']]), [
            'class' => 'btn btn-success',
            'style' => 'margin-left: 10px'
        ]);?>
    </h3>
</div>

<?= GridView::widget([
    'dataProvider' => $model['provider'],
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
    'emptyText' => 'Жилые комплексы ещё не добавлены'
]);  ?>