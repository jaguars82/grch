<?php
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\forms\AgencyForm */
/* @var $this yii\web\View */

use app\components\widgets\ImageView;
use app\components\widgets\InputAddress;
use app\components\widgets\InputFileImage;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="agency-form">
    <?php $form = ActiveForm::begin(['id' => 'agency-form', 'options' => ['enctype' => 'multipart/form-data']]); ?>

    <div class="row">       
        <div class="col-md-8">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>            
            <?= $form->field($model, 'address')->widget(InputAddress::className(), ['form' => $form]) ?>
            <?= $form->field($model, 'email')->textInput() ?>
            <?= $form->field($model, 'phone')->textInput() ?>
            <?= $form->field($model, 'url')->textInput() ?>
            <?= $form->field($model, 'user_limit')->textInput(['maxlength' => true]) ?>            
            <?= $form->field($model, 'detail')->textarea(['rows' => 15]) ?>
            <?= $form->field($model, 'offer_info')->textarea(['rows' => 8]) ?>
        </div>
        
        <div class="col-md-4">
            <?= $form->field($model, 'logo')->widget(InputFileImage::className(), ['form' => $form, 'imageWidth' => '100%']) ?>
        </div>
    </div>
    
    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>        
        <?= Html::a('Отмена', $backUrl, ['class' => 'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?= ImageView::widget() ?>