<?php

use app\assets\ProfileAsset;
use yii\helpers\Html;

$imagePath = isset($path) ? $path : '';

$this->title = 'Кабинет пользователя';
$this->params['breadcrumbs'][] = $this->title;

ProfileAsset::register($this);
?>


<?php if(!\Yii::$app->user->isGuest): ?>
<div class="row">

    <div class="col-md-3">

    <?= $this->render('/user/_sideblock') ?>

    </div>

    <div class="col-md-9">
        <div class="card white-block">
            <div class="card-body h-100">

                <div class="media">
                    <div class="media-body">
                        <h3 class="regular-title"><?= $user->fullName ?></h3>
                        <p class="regular-subtitle"><?= $user->roleLabel ?></p>
                        
                        <div class="contacts-container">
                            <h5>Контакты</h5>
                            <?php if(!is_null($user->phone)): ?>
                            <a href="tel:<?= $user->phone ?>" class="profile-contact phone">
                                <span class="material-icons-outlined">phone</span>
                                <span class="contact-label"><?= $user->phone ?></span>
                            </a>
                            <?php endif; ?>
                            <?php if(!is_null($user->email)): ?>
                            <a href="mailto:<?= $user->email ?>" class="profile-contact email">
                                <span class="material-icons-outlined">email</span>
                                <span class="contact-label"><?= $user->email ?></span>
                            </a>
                            <?php endif; ?>
                            <?php if(!is_null($user->telegram_id)): ?>
                            <a href="tg://resolve?domain=<?= $user->telegram_id ?>" class="profile-contact telegram">
                                <span class="material-icons-outlined">send</span>
                                <span class="contact-label">Telegram</span>
                            </a>
                            <?php endif; ?>
                        </div>

                    </div>
                    <div class="actions-container">
                        <?= Html::a('<div class="iconed-menu-item"><span class="material-icons-outlined">edit</span><span class="iconed-menu-label">Редактировать</span></div>', ['user/profile/update', 'id' => $user->id], ['class' => 'btn btn-primary btn-sm']) ?>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
<?php endif; ?>