<?php
/* @var $model app\models\Bank */
/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Добавить банк';
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Банки', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="bank-create white-block">
    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <div class="btn-group" style="margin: 20px 0;">
        <?= Html::a('Тарифы', null, ['class' => 'btn btn-primary', ' disabled' => 'disabled']); ?>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'backUrl' => ['index']
    ]) ?>
</div>
