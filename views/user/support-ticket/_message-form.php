<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<div class="support-message-_form">
    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'text')->label('Написать сообщение') ?>
    
        <div class="form-group">
            <?= Html::submitButton('Отправить сообщение', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div>
