<?php
use app\assets\widgets\InputFileImagesAsset;
use yii\helpers\Html;

InputFileImagesAsset::register($this);
?>

<div class="row images" style="margin-bottom: 20px; display: flex; flex-wrap: wrap;">
    <div class="col-sm-6" style="margin-bottom: 20px; padding-right: 80px; display: none">
        <?= $form->field($model, 'images[]')->fileInput(['class' => 'images-input']) ?>
        <?= Html::img(['@web/img/noimage.jpg'], [ 'class' => 'image photo', 'style' => 'max-width: 100%; height: 150px']) ?>
    </div>
    <?php if(is_array($value)) { ?>
        <?php foreach($value as $key => $data): ?>
            <div class="col-sm-6" style="margin-bottom: 20px; padding-right: 30px">
                <div style="display: none">
                    <?= $form->field($model, 'savedImages[]')->hiddenInput(['value' => $data['id']])->label(false) ?>
                </div>
                <button style="position: absolute; top: 0; right: 15px;" type="button" class="close" title="Удалить изображение"><span>&times;</span></button>
                <label style="display: block;" class="control-label" ><?= $label ?></label>
                <?= Html::img([Yii::getAlias("@web/uploads/{$data[$fileField]}")], [ 'class' => 'photo', 'style' => 'margin-top: 40px; max-width: 100%; height: 150px']) ?>
            </div>
        <?php endforeach ?>
    <?php } elseif(isset($model->savedImages)) { ?>
        <?php foreach($model->savedImages as $key => $data): ?>
            <div class="col-sm-6" style="margin-bottom: 20px; padding-right: 30px">
                <div style="display: none">
                    <?= $form->field($model, 'savedImages[]')->hiddenInput(['value' => $data['id']])->label(false) ?>
                </div>
                <button style="position: absolute; top: 0; right: 15px;" type="button" class="close" title="Удалить изображение"><span>&times;</span></button>
                <label style="display: block;" class="control-label" ><?= $label ?></label>
                <?= Html::img([Yii::getAlias("@web/uploads/{$data[$fileField]}")], [ 'class' => 'photo', 'style' => 'margin-top: 40px; max-width: 100%; height: 150px']) ?>
            </div>
        <?php endforeach ?>
    <?php } ?>
    <div class="col-sm-6" style="margin-bottom: 20px; padding-right: 80px">
        <?= $form->field($model, 'images[]')->fileInput(['class' => 'first-image last-image images-input']) //'multiple' => true, ?>
        <?= Html::img(['@web/img/noimage.jpg'], [ 'class' => 'image photo', 'style' => 'max-width: 100%; height: 150px']) ?>
    </div>
</div>