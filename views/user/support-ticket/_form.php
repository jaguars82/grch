<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * Fill some hidden fields
 */
$curr_ticket_number = $tickets_amount + 1;
$ticket_model->ticket_number = $ticket_model->author_id.'-#'.$curr_ticket_number;
// $ticket_model->is_archived = 0;

/* @var $this yii\web\View */
/* @var $ticket_model app\models\SupportTickets */
/* @var $form ActiveForm */
?>
<div class="user-support-ticket-_form">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($ticket_model, 'author_id')->hiddenInput()->label(false) ?>
        <?= $form->field($ticket_model, 'ticket_number')->hiddenInput()->label(false) ?>
        <!--<?= $form->field($ticket_model, 'is_closed') ?>-->
        <?= $form->field($ticket_model, 'has_unread_messages_from_support')->hiddenInput()->label(false) ?>
        <?= $form->field($ticket_model, 'has_unread_messages_from_author')->hiddenInput()->label(false) ?>
        <!--<?= $form->field($ticket_model, 'is_archived')->hiddenInput()->label(false) ?>-->
        <?= $form->field($ticket_model, 'last_enter_by_support')->hiddenInput()->label(false) ?>
        <?= $form->field($ticket_model, 'last_enter_by_author')->hiddenInput()->label(false) ?>
        <?= $form->field($ticket_model, 'created_at')->hiddenInput()->label(false) ?>
        <?= $form->field($ticket_model, 'updated_at')->hiddenInput()->label(false) ?>
        <?= $form->field($ticket_model, 'title') ?>
        <?= $form->field($message_model, 'author_id')->hiddenInput()->label(false) ?>
        <!--<?= $form->field($message_model, 'ticket_id')->hiddenInput()->label(false) ?>-->
        <!--<?= $form->field($message_model, 'message_number')->hiddenInput()->label(false) ?>-->
        <?= $form->field($message_model, 'author_role')->hiddenInput()->label(false) ?>
        <?= $form->field($message_model, 'text') ?>
    
        <div class="form-group">
            <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- user-support-ticket-_form -->
