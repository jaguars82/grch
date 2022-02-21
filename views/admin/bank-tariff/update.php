<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\BankTariff */

$this->title = 'Обновить тариф: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Банки', 'url' => ['admin/bank/index']];
$this->params['breadcrumbs'][] = ['label' => $bank->name, 'url' => ['admin/bank/update', 'id' => $bank->id]];
$this->params['breadcrumbs'][] = ['label' => 'Тарифы', 'url' => ['admin/bank-tariff/index', 'bankId' => $bank->id]];
$this->params['breadcrumbs'][] = ['label' => $bankTariff->name, 'url' => ['admin/bank-tariff/update', 'id' => $bankTariff->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bank-tariff-update white-block">

    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
        'bank' => $bank,
    ]) ?>

</div>
