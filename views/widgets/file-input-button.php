<?php
use app\assets\widgets\FileInputButtonAsset;
use yii\widgets\ActiveForm;

FileInputButtonAsset::register($this);
?>

<?php $form = ActiveForm::begin([
        'id' => 'load-import-file-form',
        'action' => $url,
        'enableClientValidation' => false,
        'options' => [
            'enctype' => 'multipart/form-data',
            'style' => 'display:inline-block',
            'data' => [
                'container' => $container,
            ],
        ]
]) ?>

<div style="display: none">
    <?= $form->field($model, $field)->fileInput(['id' => 'select-import-file'])->label(false) ?>
</div>

<button id="select-and-commit-btn" class="btn btn-primary" data-reinit="<?= $reinit ?>"><?= $name ?></button>

<?php ActiveForm::end() ?>
