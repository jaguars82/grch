<?php
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\models\Bank */
/* @var $this yii\web\View */

use app\components\widgets\ImageView;
use app\components\widgets\InputFileImage;
use app\components\widgets\InputAddress;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="bank-form">
    <?php $form = ActiveForm::begin(); ?>
    
    <div class="row">       
        <div class="col-md-8">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'address')->widget(InputAddress::className(), ['form' => $form]) ?>
            <?= $form->field($model, 'email')->textInput() ?>
            <?= $form->field($model, 'phone')->textInput() ?>
            <?= $form->field($model, 'url')->textInput() ?>
        </div>
        
        <div class="col-md-4">
            <?= $form->field($model, 'logo')->widget(InputFileImage::className(), ['form' => $form, 'imageResetAttribute' => 'is_logo_reset', 'imageWidth' => '100%']) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>        
        <?= Html::a('Отмена', $backUrl, ['class' => 'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?= ImageView::widget() ?>
