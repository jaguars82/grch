<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\TabularColumn;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Office */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="office-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phones')->widget(MultipleInput::className(), [
            'max' => 10,
            'min' => 0, // should be at least 2 rows
            'allowEmptyList' => true,
            'addButtonPosition' => MultipleInput::POS_FOOTER, // show add button in the header
            'attributeOptions' => [
                'enableAjaxValidation' => false,
                'enableClientValidation' => true,
                'validateOnChange' => true,
                'validateOnSubmit' => true,
                'validateOnBlur' => false,
            ],
            'columns' => [
                [
                    'name'  => 'value',
                    'type' => \yii\widgets\MaskedInput::className(),
                    'title' => 'Номер телефона',
                    'options' => [
                        'options' => [
                            'class' => 'form-control',
                        ],
                        'mask' =>  '+7 (999) 999-99-99',
                    ],
                    'enableError' => true,
                ],
                [
                    'name'  => 'owner',
                    'type'  => TabularColumn::TYPE_TEXT_INPUT,
                    'title' => 'Владелец',
                    'enableError' => true,
                ]
            ]
        ])->label(false);
    ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Отмена', $backUrl, ['class' => 'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
