<?php
use app\assets\widgets\InputAddressAsset;

InputAddressAsset::register($this);
?>

<?= $form->field($model, (!is_null($attribute) ? $attribute : 'address'), ['template' => "{label}\n{input}"])->textInput(['maxlength' => true, 'data-form-name' => $form->id, 'required' => false])->label(false) ?> 

<div style="dsplay: none">
    <?= $form->field($model, 'longitude', ['template' => "{label}\n{input}"])->hiddenInput(['id' => 'longitude'])->label(false) ?>
    <?= $form->field($model, 'latitude', ['template' => "{label}\n{input}"])->hiddenInput(['id' => 'latitude'])->label(false) ?>
</div>

