<?php
/* @var $model app\models\Bank */
/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Обновить банк: ' . $bank->name;
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Банки', 'url' => ['index']];
$this->params['breadcrumbs'][] = $bank->name;
?>

<div class="bank-update white-block">
    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <div class="btn-group" style="margin: 20px 0;">
        <?= Html::a('Тарифы', ['/admin/bank-tariff/index', 'bankId' => $bank->id], ['class' => 'btn btn-primary']); ?>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'backUrl' => ['index']
    ]) ?>
</div>
