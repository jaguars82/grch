<?php
use app\assets\HeaderAsset;
use yii\widgets\Menu;
use yii\helpers\Html;
use app\models\City;
use app\components\widgets\LocationSelect;

HeaderAsset::register($this);

$mainMenuItems = [
    ['label' => 'Новости', 'url' => ['/news']],
    ['label' => 'Застройщики', 'url' => ['/developer']],
    ['label' => 'ЖК', 'url' => ['/newbuilding-complex']],
    ['label' => 'Банки', 'url' => ['/bank']],
    ['label' => 'Агентства', 'url' => ['/agency']],
];
?>

<header class="header">
    <div class="container">
        <a class="header--logo" href="/">
            <?= Html::img(\Yii::getAlias('@web/img/icons/logo.svg'))?>
        </a>
        
        <?= LocationSelect::widget([
            'cities' => City::getAllAsList(),
        ]); ?>

        <div class="mobile-menu-icon">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <?= Menu::widget([
            'items' => $mainMenuItems,
            'activeCssClass' => 'selected',
            'options' => [
                'class' => 'header--menu'
            ]
        ]);?>

        <?php if(!Yii::$app->user->isGuest): ?>
        <div id="profile-button">
            <div class="avatar-container">
                <img src="/img/user-nofoto.jpg" class="avatar">
            </div>
            <div class="user-info-box">
                <p class="user-name">
                    <?= Yii::$app->user->identity->first_name ?> <?= Yii::$app->user->identity->last_name ?>
                </p>
                <p class="user-position">
                    <?= Yii::$app->user->identity->roleLabel ?>
                </p>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="mobile-menu">
        <div class="container">
            <?= Menu::widget([
                'items' => $mainMenuItems,
                'activeCssClass' => 'selected',
            ]);?>
        </div>
    </div>
</header>

<template id="profile-menu" type="text/x-kendo-template">
    <ul class="profile-menu-list">
        <li class="profile-menu-item">
            <a href="/user/profile/index">
                <span class="material-icons">person_outline</span><span class="item-text">Профиль</span>
            </a>
        </li>
        <?php if(Yii::$app->user->can('admin')): ?>
        <li class="profile-menu-item">
            <a href="/admin/index">
                <span class="material-icons-outlined">settings</span><span class="item-text">Админ-панель</span>
            </a>
        </li>
        <?php endif; ?>
        <?php if(Yii::$app->user->can('admin') || Yii::$app->user->can('agent')): ?>
        <li class="profile-menu-item">
            <a href="/favorite">
                <span class="material-icons">bookmark_border</span><span class="item-text">Избранное</span>
            </a>
        </li>
        <?php endif; ?>
        <li class="profile-menu-item">
            <a href="/auth/logout">
                <span class="material-icons">logout</span><span class="item-text">Выход</span>
            </a>
        </li>
    </ul>
</template>