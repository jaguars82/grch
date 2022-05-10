<?php

use yii\widgets\Pjax;
use yii\helpers\Html;

$format = \Yii::$app->formatter;

?>

<div>
    <?php Pjax::begin(['id' => 'refreshTicketsPjax', 'enablePushState' => false]); ?>

        <?= Html::beginForm(['/user/support/index'], 'post', ['data-pjax' => '', 'class' => 'form-inline']); ?>
        <?= Html::input('hidden', 'action', 'refresh_ticket') ?>
        <?= Html::input('hidden', 'watcher', \Yii::$app->user->id) ?>
        <?= Html::submitButton('обновить', ['class' => 'hidden', 'id' => 'refreshTicketsButton']) ?>
        <?= Html::endForm() ?>
        <?php foreach($tickets as $ticket): ?>
        <div class="ticket-container">
            <a href="/user/support-ticket/view?id=<?= $ticket->id ?>" data-pjax="0">
                <div>
                    <span class="ticket-number"><strong><?= $ticket->ticket_number ?></strong></span>

                    <?php if($ticket->unreadFromAdmin): ?>
                        <span class="material-icons-outlined icon-indicator">mark_chat_unread</span>
                    <?php endif; ?>
                    <?php if($ticket->unreadFromAuthor): ?>
                        <span class="material-icons-outlined icon-indicator">mark_chat_unread</span>
                    <?php endif; ?>
                </div>
                <div><span class="text-muted">создан <?= $format->asUpdateDate($ticket->created_at) ?></span></div>
                <div class="ticket-title">
                    <p><?= $ticket->title ?></p>
                </div>
            </a>
            <?php if(\Yii::$app->user->can('admin')): ?>
                <div class="support-ticket-footer">
                    <!--<hr />-->
                    <?= $this->render('/widgets/user-badge', [
                        'name' => $ticket->authorName,
                        'surname' => $ticket->authorSurname,
                        'avatar' => $ticket->authorAvatar,
                        'role' => $ticket->authorRole,
                        'agency' => $ticket->authorAgency,
                    ]);
                    ?>
                    <div>
                        <?= $this->render('/widgets/iconed-button', [
                            'icon_class' => 'material-icons-outlined',
                            'icon' => $ticket->is_archived == 1 ? 'unarchive' : 'archive'
                        ])
                        ?>
                        <?= $this->render('/widgets/iconed-button', [
                            'icon_class' => 'material-icons-outlined',
                            'icon' => $ticket->is_closed == 1 ? 'lock_open' : 'lock'
                        ])
                        ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    <?php Pjax::end(); ?>   
    
</div>
