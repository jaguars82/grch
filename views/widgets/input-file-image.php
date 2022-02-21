<?php
use app\assets\widgets\InputFileImageAsset;
use yii\helpers\Html;

$data = ['class' => 'photo input-file-img'];

if (isset($imageHeight)) {
    $data['height'] = $imageHeight;
}

if (isset($imageWidth)) {
    $data['width'] = $imageWidth;
}

$filename = is_null($model->$attribute) ? "@web/img/noimage.jpg" : "@web/uploads/{$model->$attribute}";

InputFileImageAsset::register($this);
?>

<?= $form->field($model, $attribute, ['template' => "{label}\n{input}"])->fileInput(['class' => 'image-input'])->label(false) ?>

<div class="image-block" style="position: relative">
    <?= Html::img([Yii::getAlias($filename)], $data) ?>
    
    <?php if (!is_null($imageResetAttribute)): ?>
        <button type="button" class="close_btn" style="position: absolute; left: 0; top: -5px; <?php if(is_null($model->$attribute)): ?>display: none<?php endif ?>" title="Сбросить изображение">&times;</button>
    <?php endif ?>
</div>

<?php if (!is_null($imageResetAttribute)): ?>
    <div style="display: none"><?= $form->field($model, $imageResetAttribute)->textInput(['class' => 'is_image_reset'])->label(false)?></div>
<?php endif ?>