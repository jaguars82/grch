<?php
use app\assets\HeaderAsset;
use yii\widgets\Menu;
use yii\helpers\Html;
use app\models\City;
use app\models\Region;
use app\components\widgets\LocationSelect;
use app\components\async\ParamsGet;

$events = (new ParamsGet())->getAllEventsParams();

HeaderAsset::register($this);

$mainMenuItems = [
    ['label' => 'Новости', 'url' => ['/news']],
    ['label' => 'Застройщики', 'url' => ['/developer']],
    ['label' => 'ЖК', 'url' => ['/newbuilding-complex']],
    // ['label' => 'Банки', 'url' => ['/bank']],
    // ['label' => 'Агентства', 'url' => ['/agency']],
    ['label' => 'Контакты', 'url' => ['/agency']],
    ['label' => 'Тарифы', 'url' => ['/tariff']],
];

$user = Yii::$app->user->identity;
?>

<header class="header">
    <div class="container">
        <a class="header--logo" href="/">
            <?= Html::img(\Yii::getAlias('@web/img/icons/logo.svg'))?>
        </a>
        
        <?= LocationSelect::widget([
            'cities' => City::getAllAsList(),
            'regions' => Region::find()->orderBy(['name' => SORT_ASC])->all(),
        ]); ?>

        <?= Menu::widget([
            'items' => $mainMenuItems,
            'activeCssClass' => 'selected',
            'options' => [
                'class' => 'header--menu'
            ]
        ]);?>


        <div id="main-menu-left-container">        

        <?php if(!Yii::$app->user->isGuest): ?>
        <div id="profile-button">
            <div class="avatar-container">
                <?php if(!is_null($user->photo)): ?>
                    <?= Html::img(\Yii::getAlias("@web/uploads/{$user->photo}"), [ 'class' => 'avatar']); ?>
                <?php else: ?>
                    <img src="/img/user-nofoto.jpg" class="avatar">
                <?php endif; ?>

                <div class="event-indicator">
                    <?= $this->render('/widgets/status-indicator', [
                        'url' => '/async/params-get/event',
                        'action' => 'refreshEvents',
                        'status' => $events['status'],
                        'amount' => $events['amount']
                    ]) ?>
                </div>
            </div>
            <div class="user-info-box hidden-xs">
                <p class="user-name">
                    <?= $user->first_name ?> <?= $user->last_name ?>
                </p>
                <p class="user-position">
                    <?= $user->roleLabel ?>
                </p>
            </div>
        </div>
        <?php endif; ?>

        <div class="mobile-menu-icon">
            <span></span>
            <span></span>
            <span></span>
        </div>

        </div>

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
                <span class="material-icons">person_outline</span><span class="item-text">Личный кабинет</span>
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