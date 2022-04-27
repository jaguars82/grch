<?php

use app\assets\SupportAsset;
use app\models\form\SupportMessageForm;
use yii\widgets\Pjax;
use yii\helpers\Html;

$format = \Yii::$app->formatter;

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

            <h3 class="regular-title"><?= $ticket->ticket_number ?></h3>
            <p class="regular-subtitle"><?= $ticket->title ?></p>

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
                    <!--<pre><?= var_dump($messages) ?></pre>-->
                    <?php foreach($messages as $message): ?>
                    <div class="message <?= $message->author_id == \Yii::$app->user->id ? 'self' : '' ?>">
                        <div><?= $message->text ?></div>
                        <?= $this->render('/widgets/user-badge', [
                            'name' => $message->authorName,
                            'surname' => $message->authorSurname,
                            'avatar' => $message->authorAvatar,
                        ]) ?>
                        <div><span class="text-muted"><?= $format->asUpdateDate($message->created_at) ?></span></div>
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

        </div>
    </div>

</div>
<?php endif; ?>