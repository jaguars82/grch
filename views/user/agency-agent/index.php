<?php
/* @var $this yii\web\View */

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

$this->title = 'Администраторы';
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Агентства недвижимости', 'url' => ['admin/agency/index']];
$this->params['breadcrumbs'][] = ['label' => $agency->name, 'url' => ['admin/agency/update', 'id' => $agency->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="white-block">
    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>
    
    <div style="margin: 20px 0;" class="btn-group">
        <p><?= Html::a('Добавить агента', ['create', 'agencyId' => $agency->id], ['class' => 'btn btn-success', 'style' => 'margin-bottom: 10px']) ?></p>
    </div>

    <?php Pjax::begin([
        'id' => 'search-result',
        'enablePushState' => true,
        'enableReplaceState' => false,
    ]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => '',
        'emptyText' => 'Агенты ещё не добавлены',
        'columns' => [
            [
                'attribute' => 'fullName',
                'value' => function ($agent) {
                    // return Html::a($agent->fullName, ['admin/user/agency-agent/update', 'id' => $agent->id]);
                    return Html::a($agent->fullName, ['user/agency-agent/update', 'agencyId' => $agent->agency_id, 'id' => $agent->id]);
                },
                'format' => 'raw',
            ],
            'email:email',
            [
                'attribute' => 'phone',
                'value' => function ($agent) {
                    return !is_null($agent->phone) ? \Yii::$app->formatter->asPhoneLink($agent->phone) : null;
                },
                'format' => 'raw',
            ],
            'telegram_id',
            [
                'class' => 'yii\grid\ActionColumn',
                'visibleButtons' => ['view' => false],
                /*'buttons' => [
                    'update' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                                    'title' => Yii::t('app', 'lead-view'),
                        ]);
                    },
                ]*/
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>
</div>