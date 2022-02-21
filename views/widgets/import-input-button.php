<?php
use app\assets\widgets\ImportInputButtonAsset;
use yii\widgets\ActiveForm;

ImportInputButtonAsset::register($this);
?>

<?php $form = ActiveForm::begin([
        'id' => 'load-archive-file-form',
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
    <?= $form->field($model, $field)->fileInput(['id' => 'select-archive-file'])->label(false) ?>
</div>

<button id="select-and-commit-archive-btn" class="btn btn-primary"><?= $name ?></button>

<button style="display: none" id="archive-submit" class="btn btn-success" data-reinit="<?= $reinit ?>">Загрузить</button>

<?php ActiveForm::end() ?>
