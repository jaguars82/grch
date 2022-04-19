<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$model->author_id = 110;
$model->ticket_number = '78342jk'

/* @var $this yii\web\View */
/* @var $model app\models\SupportTickets */
/* @var $form ActiveForm */
?>
<div class="user-support-ticket-_form">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'author_id')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'ticket_number')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'is_closed') ?>
        <?= $form->field($model, 'has_unread_messages_from_support')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'has_unread_messages_from_author')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'is_archived') ?>
        <?= $form->field($model, 'last_enter_by_support')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'last_enter_by_author')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'created_at')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'updated_at')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'title') ?>
    
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- user-support-ticket-_form -->
