<?php

use app\assets\SupportAsset;
use app\models\form\SupportMessageForm;

$message_form = new SupportMessageForm;

$this->title = 'Запрос '.$ticket->ticket_number;
$this->params['breadcrumbs'][] = ['label' => 'Кабинет пользователя', 'url' => ['user/profile/index']];
$this->params['breadcrumbs'][] = ['label' => 'Техподдержка', 'url' => ['user/support/index']];
$this->params['breadcrumbs'][] = $this->title;

SupportAsset::register($this);

?>

<?php if(!\Yii::$app->user->isGuest): ?>
<div class="row">

    <div class="col-md-3">

    <?= $this->render('/user/_sideblock') ?>

    </div>

    <div class="col-md-9">
        <div class="white-block">

            <p>
                <?= $ticket->ticket_number ?>
                <?= $ticket->title ?>

                <div class="conversation-box">
                    <?php foreach($messages as $message): ?>
                    <div class="message <?= $message->author_id == \Yii::$app->user->id ? 'self' : '' ?>">
                        <?= $message->text ?>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?=
                    $this->render('_message-form', [
                        'model' => $message_form,
                    ]);
                ?>
            </p>

        </div>
    </div>

</div>
<?php endif; ?>