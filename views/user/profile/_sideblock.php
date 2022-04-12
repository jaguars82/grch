<?php

use app\assets\ProfileAsset;
use yii\helpers\Html;

ProfileAsset::register($this);

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
            <a href="#" class="iconed-menu-item list-group-item">
                <span class="material-icons-outlined">account_box</span>
                <span class="iconed-menu-label">Основные сведения</span>
            </a>
            <a href="#" class="iconed-menu-item list-group-item">
                <span class="material-icons-outlined">support_agent</span>
                <span class="iconed-menu-label">Техподдержка</span>
            </a>
        </div>
    </div>
</div>