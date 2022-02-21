<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\widgets\InputAddress;

/* @var $this yii\web\View */
/* @var $model app\models\City */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="city-form">

    <?php $form = ActiveForm::begin(); ?>
    
    <?= $form->field($model, 'region_id', [
        'options' => [
            'class' => 'form-group inline-select'
        ]
    ])->dropDownList($regions, [
        'prompt' => '',
        'data-placeholder' => 'Район'
    ])->label(false) ?>

    <?= $form->field($model, 'name')->widget(InputAddress::className(), ['form' => $form, 'attribute' => 'name']) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Отмена', $backUrl, ['class' => 'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
