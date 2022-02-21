<?php
/* @var $form yii\widgets\ActiveForm */
/* @var $newbuildingComplex app\models\NewbuildingComplex */
/* @var $this yii\web\View */

use app\assets\NewbuildingComplexFormAsset;
use app\components\widgets\ImageView;
use app\components\widgets\InputFileImage;
use app\components\widgets\InputFileImages;
use app\components\widgets\InputSelect;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

NewbuildingComplexFormAsset::register($this);
?>

<div class="newbuilding-complex-form">
    <?php $form = ActiveForm::begin(['id' => 'newbuilding-complex-form']); ?>
    
    <div style="display: none"><?= $form->field($model, 'newbuilding_complex_id')->hiddenInput()->label(false) ?></div>
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?> 
        <?= $form->field($model, 'detail')->textarea(['rows' => 7]) ?>            
        <?= InputFileImages::widget(['form' => $form, 'model' => $model, 'fileField' => 'image', 'label' => 'Изображение отделки']) ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Отмена', ['admin/furnish/index', 'newbuildingComplexId' => $newbuildingComplex->id], ['class' => 'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?= ImageView::widget() ?>
