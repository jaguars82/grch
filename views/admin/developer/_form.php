<?php
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\forms\DeveloperForm */
/* @var $this yii\web\View */

use app\components\widgets\ImageView;
use app\components\widgets\ImportData;
use app\components\widgets\InputAddress;
use app\components\widgets\InputFileImage;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\assets\DeveloperFormAsset;

DeveloperFormAsset::register($this);
?>
<div class="developer-form">
    <?php $form = ActiveForm::begin(['id' => 'developer-form', 'options' => ['enctype' => 'multipart/form-data']]); ?>

    <div class="row">        
        <div class="col-md-8">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>            
            <?= $form->field($model, 'address')->widget(InputAddress::className(), ['form' => $form]) ?>
            <?= $form->field($model, 'phone')->textInput() ?>
            <?= $form->field($model, 'url')->textInput() ?>
            <?= $form->field($model, 'email')->textInput() ?>
            <?= $form->field($model, 'detail')->textarea(['rows' => 15]) ?>
            <?= $form->field($model, 'offer_info')->textarea(['rows' => 8]) ?>
            <?= $form->field($model, 'free_reservation')->textarea(['rows' => 8]) ?>            
            <?= $form->field($model, 'paid_reservation')->textarea(['rows' => 8]) ?>
            <?= $form->field($model, 'sort')->textInput(['maxlength' => true]) ?>
            <?= ImportData::widget(['form' =>$form, 'model' => $import]) ?>
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