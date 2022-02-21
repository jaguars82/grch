<?php
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\forms\UserForm */
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\widgets\InputFileImage;
?>

<div class="agency-form">
    <?php $form = ActiveForm::begin(); ?>

    <div style="display: none">
        <?= $form->field($model, 'agency_id')->hiddenInput()->label(false) ?>
    </div>

    <div class="row">
        <div class="col-md-8">
            <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>            
            <?= $form->field($model, 'middle_name')->textInput(['maxlength' => true]) ?>            
            <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?> 
            <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>            
            <?= $form->field($model, 'telegram_id')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'telegram_chat_id')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'photo')->widget(InputFileImage::className(), ['form' => $form, 'imageResetAttribute' => 'is_photo_reset', 'imageWidth' => '100%']) ?>
        </div>
    </div>
    
    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>        
        <?= Html::a('Отмена', [$redirectUrl, 'id' => $model->agency_id], ['class' => 'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>