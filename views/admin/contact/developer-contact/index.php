<?php

use yii\helpers\Html;
use yii\widgets\ListView;

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Застройщики', 'url' => ['admin/developer/index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['admin/developer/update', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Контакты';
?>
<div class="white-block">
    <?php if(\Yii::$app->user->can('admin')): ?>
        <p style="margin-bottom: 20px;">
            <?= Html::a('Добавить контакт', 
                ['admin/contact/developer-contact/create', 'developerId' => $model->id],
                ['class' => 'btn btn-success']) 
            ?>
        </p>
    <?php endif ?>

    <?= ListView::widget([
        'dataProvider' => $contactDataProvider,
        'itemView' => '/common/_item-contact',
        'viewParams' => [
            'itemContactClass' => 'col-xs-12 col-sm-6',
            'route' => 'admin/contact/developer-contact',
            'noControls' => !\Yii::$app->user->can('admin'),
        ],
        'beforeItem' => function ($model, $key, $index, $widget) { 
            if ($index % 3 == 0) {
                return "<div class=\"row\">";
            }                 
        },
        'afterItem' => function ($model, $key, $index, $widget) {
            if ($widget->dataProvider->pagination) {
                $currentItem = $widget->dataProvider->pagination->page * $widget->dataProvider->pagination->pageSize  + $index;
                $isLastItem = $widget->dataProvider->getTotalCount() - $currentItem - 1 == 0;
                if ($index % 3 == 2 || $isLastItem) {
                    return "</div>";
                }
            }
        },
        'summary' => '',
        'emptyText' => 'Контакты ещё не добавлены'
    ]);  ?>
</div>