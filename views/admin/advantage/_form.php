<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\widgets\InputFileImage;

/* @var $this yii\web\View */
/* @var $model app\models\Advantage */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="advantage-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-xs-12 col-md-8">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-xs-12 col-md-4">
            <?= $form->field($model, 'icon')->widget(InputFileImage::className(), ['form' => $form, 'imageResetAttribute' => 'is_icon_reset', 'imageWidth' => '100%']) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Отмена', $backUrl, ['class' => 'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
