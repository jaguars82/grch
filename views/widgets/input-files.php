<?php
use app\assets\widgets\InputFilesAsset;

InputFilesAsset::register($this);
?>

<div class="row news-files" style="margin-bottom: 20px">
    <div class="col-md-4" style="margin-bottom: 20px; display: none">
        <?= $form->field($model, 'files[]')->fileInput(['multiple' => true, 'class' => 'news-file-input']) ?>
    </div>

    <?php foreach($model->savedFiles as $key => $file): ?>
    <div class="col-md-4">
        <div style="display: none">
            <?= $form->field($model, 'savedFiles[]')->hiddenInput(['value' => $file->id])->label(false) ?>
        </div>
        <button type="button" class="close" ><span>&times;</span></button>
        <label class="control-label" >Документ</label>
        <p style="margin-top: 3px"><?= $file->name ?></p>
    </div>
    <?php endforeach ?>

    <div class="col-md-4" style="margin-bottom: 20px">
        <?= $form->field($model, 'files[]')->fileInput(['multiple' => true, 'class' => 'first-file last-file news-file-input']) ?>
    </div>
</div>