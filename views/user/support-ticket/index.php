<?php
/* @var $this yii\web\View */
?>

<p>
    <?php foreach($tickets as $ticket): ?>
    <a href="/user/support-ticket/view?id=<?= $ticket->id ?>">
        <div>
        <?= $ticket->ticket_number ?>
        <?= $ticket->title ?>
        </div>
    </a>
    <?php endforeach; ?>
</p>
