<?php
/* @var $this yii\web\View */

use app\assets\SiteIndexAsset;
use yii\helpers\Html;
use yii\widgets\ListView;
use app\models\Flat;
use yii\helpers\Url;

$this->title = 'Главная';

$format = \Yii::$app->formatter;
SiteIndexAsset::register($this);
?>

<?= $this->render('/common/_simple-search', [
    'model' => $searchModel,
    'districts' => $districts,
    'developers' => $developers,
    'newbuildingComplexes' => $newbuildingComplexes,
])?>

<section class="usefull-links container">
    <p class="h2">
        Полезные ссылки
    </p>
    <div class="row flex-row">
        <div class="col-lg-6 col-xs-12">
            <div class="row flex-row"> 
                <div class="col-xs-6">
                    <div class="link-list rooms-count">
                        <p class="h3 bordered">
                            По кол-ву комнат
                        </p>
                        <a class="link-list--item" href="<?= Url::to(['site/search', 'AdvancedFlatSearch[roomsCount][0]' => 1]) ?>">
                            <span>1-комнатные</span>
                            <span>
                                <?= Flat::getWithRooms(1)->onlyActive()->count(); ?>
                            </span>
                        </a>
                        <a class="link-list--item" href="<?= Url::to(['site/search', 'AdvancedFlatSearch[roomsCount][0]' => 2]) ?>">
                            <span>2-комнатные</span>
                            <span>
                                <?= Flat::getWithRooms(2)->onlyActive()->count(); ?>
                            </span>
                        </a>
                        <a class="link-list--item" href="<?= Url::to(['site/search', 'AdvancedFlatSearch[roomsCount][0]' => 3]) ?>">
                            <span>3-комнатные</span>
                            <span>
                                <?= Flat::getWithRooms(3)->onlyActive()->count(); ?>
                            </span>
                        </a>
                        <a class="link-list--item" href="<?= Url::to(['site/search', 'AdvancedFlatSearch[flatType]' => \app\models\search\AdvancedFlatSearch::FLAT_TYPE_STUDIO]) ?>">
                            <span>Квартиры-студии</span>
                            <span>
                                <?= Flat::getStudio()->onlyActive()->count(); ?>
                            </span>
                        </a>
                    </div>
                </div>
                <div class="col-xs-6">
                    <div class="link-list deadline">
                        <p class="h3 bordered">
                            По сроку сдачи
                        </p>
                        <a class="link-list--item" href="<?= Url::to(['site/search', 'AdvancedFlatSearch[newbuilding_status]' => \app\models\Newbuilding::STATUS_FINISH])?>">
                            <span>Сданные</span>
                            <span>
                                <?= Flat::getSurrendered()->onlyActive()->count() ?>
                            </span>
                        </a>
                        <a class="link-list--item" href="<?= Url::to(['site/search', 'AdvancedFlatSearch[deadlineYear]' =>  date('Y')])?>">
                            <span>До конца года</span>
                            <span>
                                <?= Flat::getWithEndYearDeadline()->onlyActive()->count() ?>
                            </span>
                        </a>
                        <a class="link-list--item" href="<?= Url::to(['site/search', 'AdvancedFlatSearch[deadlineYear]' =>  date('Y', strtotime('+1 year'))])?>">
                            <span>Сдача в <?= date('Y', strtotime('+1 year')) ?></span>
                            <span>
                                <?= Flat::getAfterYearsDeadline(1)->onlyActive()->count() ?>
                            </span>
                        </a>
                        <a class="link-list--item" href="<?= Url::to(['site/search', 'AdvancedFlatSearch[deadlineYear]' =>  date('Y', strtotime('+2 year'))])?>">
                            <span>Сдача в <?= date('Y', strtotime('+2 year')) ?> и после</span>
                            <span>
                                <?= Flat::getAfterYearsDeadline(2)->onlyActive()->count() ?>
                            </span>
                        </a>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="inline-list stock">
                        <p class="h3 bordered">
                            Акции
                        </p>
                        <?= ListView::widget([
                            'dataProvider' => $actionsDataProvider,
                            'itemView' => '/common/_inline-list-item',
                            'summary' => '',
                            'emptyText' => 'Акций ещё нет',
                            'viewParams' => [
                                'displayType' => false
                            ],
                        ]); ?>
                        <?php if ($actionsDataProvider->totalCount > 1): ?>
                            <div class="center">
                                <?= Html::a('Все акции', ['/news', 'action-page' => 1], ['class' => 'btn btn-white']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-xs-12">
            <div class="inline-list news">
                <p class="h3 bordered">
                    Новости
                </p>
                <?= ListView::widget([
                    'dataProvider' => $newsDataProvider,
                    'itemView' => '/common/_inline-list-item',
                    'summary' => '',
                    'emptyText' => 'Новостей ещё нет',
                    'viewParams' => [
                        'displayType' => false
                    ],
                ]); ?>
                <?php if ($newsDataProvider->totalCount > 3): ?>
                    <div class="center">
                        <?= Html::a('Все новости', ['/news', 'news-page' => 1], ['class' => 'btn btn-white']) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section class="developer-block">
    <div class="container">
        <p class="h2">
            Застройщики
        </p>
    </div>
    <div class="common-slider swiper">
        <?= ListView::widget([
            'dataProvider' => $developerDataProvider,
            'itemView' => '/common/_developer-item',
            'itemOptions' => [
                'tag' => false,
            ],
            'options' => [
                'class' => 'swiper-wrapper'
            ],
            'summary' => '',
            'emptyText' => '',
        ]); ?>
    </div> 
</section>

<section class="agency-block">
    <div class="container">
        <p class="h2">
            Агентства
        </p>
    </div>
    <div class="common-slider swiper">
        <?= ListView::widget([
            'dataProvider' => $agencyDataProvider,
            'itemView' => '/common/_agency-item',
            'itemOptions' => [
                'tag' => false,
            ],
            'options' => [
                'class' => 'swiper-wrapper'
            ],
            'viewParams' => [
                'colClass' => 'swiper-slide slider-card hover-accent'
            ],
            'summary' => '',
            'emptyText' => '',
        ]); ?>
    </div>
</section>

<section class="bank-block">
    <div class="container">
        <p class="h2">
            Банки
        </p>
    </div>
    <div class="common-slider swiper">
        <?= ListView::widget([
            'dataProvider' => $bankDataProvider,
            'itemView' => '/common/_bank-item',
            'itemOptions' => [
                'tag' => false,
            ],
            'options' => [
                'class' => 'swiper-wrapper'
            ],
            'viewParams' => [
                'colClass' => 'swiper-slide slider-card hover-item'
            ],
            'summary' => '',
            'emptyText' => '',
        ]); ?>
    </div>
</section>