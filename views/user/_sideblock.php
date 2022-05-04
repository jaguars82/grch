<?php

use app\assets\ProfileAsset;
use yii\helpers\Html;
use app\components\async\ParamsGet;

ProfileAsset::register($this);

$user = \Yii::$app->user->identity;

$support_messages_amount = (new ParamsGet())->getSupportMessagesAmount();

?>

<div class="card white-block left-sidebar">
    <button class="btn btn-primary left-sidebar-button">Меню</button>
    <div class="card-body text-center">
        <?php if(!is_null($user->photo)): ?>
            <?= Html::img(\Yii::getAlias("@web/uploads/{$user->photo}"), [ 'class' => 'img-circle mb-2', 'width' => 200, 'height' => 200]); ?>
        <?php else: ?>
            <?= Html::img(\Yii::getAlias("@web/img/user-nofoto.jpg"), [ 'class' => 'img-circle mb-2', 'width' => 200, 'height' => 200]); ?>
        <?php endif; ?>
        <h4 class="card-title"><?= $user->first_name ?> <?= $user->last_name ?></h4>
        <div class="text-muted"><?= $user->roleLabel ?></div>

    </div>
    <div class="card-body">
        <div class="list-group profile-menu-container">
            <a href="/user/profile/index" class="iconed-menu-item list-group-item">
                <span class="material-icons-outlined">account_box</span>
                <span class="iconed-menu-label">Профиль</span>
            </a>
            <?php if(Yii::$app->user->can('manager')): ?>
            <a href="/user/agency-agent/index?agencyId=<?= $user->agency_id ?>" class="iconed-menu-item list-group-item">
                <span class="material-icons-outlined">people</span>
                <span class="iconed-menu-label">Агенты</span>
            </a>
            <?php endif; ?>
            <a href="/user/support/index" class="iconed-menu-item list-group-item">
                <span class="material-icons-outlined">support_agent</span>
                <span class="iconed-menu-label">Техподдержка</span>
                <div class="iconed-menu-indicator">
                    <?= $this->render('/widgets/status-indicator', [
                        'url' => '/async/params-get/support-messages',
                        'action' => 'refreshSupportMessagesAmount',
                        'status' => $support_messages_amount > 0 ? true : false,
                        'amount' => $support_messages_amount,
                    ]) ?>
                </div>
            </a>
        </div>
    </div>
</div>