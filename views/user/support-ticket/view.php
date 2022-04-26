<?php

use app\assets\SupportAsset;
use app\models\form\SupportMessageForm;
use yii\widgets\Pjax;
use yii\helpers\Html;

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
                    <!--
                    <?php foreach($messages as $message): ?>
                    <div class="message <?= $message->author_id == \Yii::$app->user->id ? 'self' : '' ?>">
                        <?= $message->text ?>
                    </div>
                    <?php endforeach; ?>
                    -->

                    <?php Pjax::begin(); ?>
                    <?= Html::beginForm(['view?id='.$ticket->id.''], 'post', ['data-pjax' => '', 'class' => 'form-inline']); ?>
                        <?= Html::input('hidden', 'id', $ticket->id) ?>
                        <?= Html::input('hidden', 'action', 'refresh') ?>
                        <?= Html::submitButton('обновить', ['class' => 'hidden', 'id' => 'refreshButton']) ?>
                        <!--<?= Html::a(
                        'Обновить',
                        ['refresh'],
                        ['class' => 'btn btn-lg btn-primary', 'id' => 'refreshButton']
                        ) ?>-->
                        <?= Html::endForm() ?>
                        <?php foreach($messages as $message): ?>
                        <div class="message <?= $message->author_id == \Yii::$app->user->id ? 'self' : '' ?>">
                            <div><?= $message->text ?></div>
                            <div><span class="text-muted"><?= $message->created_at ?></span></div>
                        </div>
                        <div class="message-separator"></div>
                        <?php endforeach; ?>
                    <?php Pjax::end(); ?>

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