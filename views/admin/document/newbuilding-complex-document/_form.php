<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\assets\widgets\InputFilesAsset;

InputFilesAsset::register($this);

/* @var $this yii\web\View */
/* @var $model app\models\Document */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="document-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'file')->fileInput([
        'class' => 'file-input'
    ]) ?>


    <?php if(isset($document) && !is_null($document) && !is_null($document->file)): ?>
        <p>
            <?= Html::a($document->file, ['/newbuilding-complex/download-document', 'id' => $document->id])?>
        </p>
    <?php endif; ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
