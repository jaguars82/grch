<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\BankTariff */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bank-tariff-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>


    <?= $form->field($model, 'yearlyRateAsPercent', [
        'template' => '
            {label}
            <div class="input-group">
                {input}
                <div class="input-group-addon"><span style="width: 26px; display: inline-block">%</span></div>
            </div>
            {hint}{error}'
    ])->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'initialFeeRateAsPercent', [
        'template' => '
            {label}
            <div class="input-group">
                {input}
                <div class="input-group-addon"><span style="width: 26px; display: inline-block">%</span></div>
            </div>
            {hint}{error}'
    ])->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'payment_period', [
        'template' => '
            {label}
            <div class="input-group">
                {input}
                <div class="input-group-addon">мес.</div>
            </div>
            {hint}{error}'
    ])->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Отмена', ['index', 'bankId' => $bank->id], ['class' => 'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
