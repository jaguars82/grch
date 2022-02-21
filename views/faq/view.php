<?php
/* @var $this yii\web\View */

use yii\helpers\Html;

$format = \Yii::$app->formatter;

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Помощь', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$format = \Yii::$app->formatter;
?>

<div class="faq-view">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if(Yii::$app->user->can('admin')): ?>
        <p style="margin: 20px 0;">
            <?= Html::a('Обновить запись', ['admin/faq/update', 'id' => $model->id], ['class' => 'btn btn-primary']); ?>
        </p>
    <?php endif; ?>

    <div class="faq-text">
        <?= $format->asHtml($model->text) ?>
    </div>
</div>