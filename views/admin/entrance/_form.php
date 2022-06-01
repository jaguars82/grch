<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use app\models\Entrance;

/* @var $this yii\web\View */
/* @var $model app\models\form\EntranceForm */
/* @var $form ActiveForm */
?>
<div class="form">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'name') ?>
        <?= $form->field($model, 'number') ?>
        <?= $form->field($model, 'floors') ?>
        <?= $form->field($model, 'material') ?>
        <?= $form->field($model, 'azimuth') ?>
        <?= $form->field($model, 'status', [
                'options' => [
                    'class' => 'form-group inline-select'
                ]
            ])->dropDownList(Entrance::$status, [
                'prompt' => '',
                'data-placeholder' => 'Статус'
            ]) ?>
            <?= $form->field($model, 'deadline')->widget(DatePicker::class,[
                'dateFormat' => 'dd.MM.yyyy',
                'options' => ['class'=> 'form-control'],
            ]) ?>
    
        <div class="form-group">
            <?= Html::submitButton('Сохранить изменения', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- _form -->
