<?php

use app\assets\ProfileAsset;
use yii\helpers\Html;

ProfileAsset::register($this);

?>

<div class="card white-block mb-3">
    <div class="card-header">
        <h5 class="card-title mb-0 text-center text-uppercase"><?= $this->title ?></h5>
    </div>
    <div class="card-body text-center">
        <?php if(!is_null($user->photo)): ?>
            <?= Html::img(\Yii::getAlias("@web/uploads/{$user->photo}"), [ 'class' => 'img-circle mb-2', 'width' => 200, 'height' => 200]); ?>
        <?php else: ?>
            <?= Html::img(\Yii::getAlias("@web/img/user-nofoto.jpg"), [ 'class' => 'img-circle mb-2', 'width' => 200, 'height' => 200]); ?>
        <?php endif; ?>
        <h4 class="card-title mb-0"><?= $user->first_name ?> <?= $user->last_name ?></h4>
        <div class="text-muted mb-2"><?= $user->roleLabel ?></div>

    </div>
    <div class="card-body">
        <div class="list-group">
            <a href="#" class="list-group-item">
                <span class="material-icons-outlined">account_box</span>
                Основные сведения
            </a>
            <a href="#" class="list-group-item">
                <span class="material-icons-outlined">support_agent</span>
                Техподдержка
            </a>
        </div>
    </div>
</div>