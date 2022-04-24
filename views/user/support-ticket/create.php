<?php

use app\assets\ProfileAsset;
use app\assets\SupportAsset;
use yii\helpers\Html;

$imagePath = isset($path) ? $path : '';

$this->title = 'Создание запроса в техподдержку';
$this->params['breadcrumbs'][] = ['label' => 'Кабинет пользователя', 'url' => ['user/profile/index']];
$this->params['breadcrumbs'][] = ['label' => 'Техподдержка', 'url' => ['user/support/index']];
$this->params['breadcrumbs'][] = $this->title;

// ProfileAsset::register($this);
// SupportAsset::register($this);

?>

<?php if(!\Yii::$app->user->isGuest): ?>
<div class="row">

    <div class="col-md-3">

    <?= $this->render('/user/_sideblock') ?>

    </div>

    <div class="col-md-9">
        <div class="white-block">
        
        <div class="ticket-create">
                <h1><?= Html::encode($this->title) ?></h1>

                <?= $this->render('/user/support-ticket/_form', [
                    'tickets_amount' => $tickets_amount,
                    'ticket_model' => $ticket_model,
                    'message_model' => $message_model,
                ]) ?>
            </div>

        </div>
    </div>

</div>
<?php endif; ?>