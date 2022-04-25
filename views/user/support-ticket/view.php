<?php

use app\models\form\SupportMessageForm;

$message_form = new SupportMessageForm;

?>

<p>
    <?= $ticket->ticket_number ?>
    <?= $ticket->title ?>

    <?php foreach($messages as $message): ?>
        <?= $message->message_number ?>
        <?= $message->text ?>
    <?php endforeach; ?>

    <?=
        $this->render('_message-form', [
            'model' => $message_form,
        ]);
    ?>
</p>