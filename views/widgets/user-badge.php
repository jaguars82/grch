<?php
use app\assets\widgets\UserBadgeAsset;
use yii\helpers\Html;

UserBadgeAsset::register($this);
?>

<div class="user-badge iconed-menu-item">
    <?php if(!is_null($avatar)): ?>
        <?= Html::img(\Yii::getAlias("@web/uploads/{$avatar}"), [ 'class' => 'avatar']); ?>
    <?php else: ?>
        <img src="/img/user-nofoto.jpg" class="avatar">
    <?php endif; ?>                   
    <div class="iconed-menu-label"><span><?= $name ?> <?= $surname ?></span></div>
</div>