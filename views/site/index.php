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

<section class="media-bckgr search-index">
    <?= $this->render('/common/_simple-search', [
        'model' => $searchModel,
        'districts' => $districts,
        'developers' => $developers,
        'newbuildingComplexes' => $newbuildingComplexes,
    ])?>

    <div class="container">
        <div class="row">
            <div class="col-md-6 col-sm-12 col-xs-12">

                <div class="dark-transparent-block" style="margin-top: 15px; padding-bottom: 0;">
                    <div id="news-slider" style="height: 240px; border: none; background: transparent;">
                        <?php foreach ($newsList as $newsItem): ?>
                        <div data-role="page">
                            <div>
                                <h3 class="bordered regular-title" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #fff;">
                                    <span style="font-weight: 400; padding-right: 10px;"><?= $newsItem->title ?></span>
                                </h3>
                                <table style="margin-top: 20px;">
                                    <tr>
                                <?php if(isset($newsItem->image) && !empty($newsItem->image)): ?>
                                    <td style="width: 150px; vertical-align: top; padding-left: 5px; padding-right: 15px;">
                                        <img src="/uploads/<?= $newsItem->image ?>">
                                    </td>
                                <?php endif; ?>
                                <td>
                                    <p>
                                        <span class="<?= $newsItem->isAction() ? 'bage-action' : 'bage-news'?>"></span>
                                        <span style="padding-left: 10px; color: #aaa !important;"><?= $format->asDate($newsItem->created_at, 'php:d.m.Y') ?></span>
                                        <a href="/news/view?id=<?= $newsItem->id ?>">
                                        <span class="material-icons-outlined" style="padding-left: 10px; font-size: 1.2em; color: #bbb;">open_in_new<span>
                                    </a>
                                    </p>
                                    <div style="white-space: normal; height: 60px; max-height: 60px; overflow: hidden;">
                                        <p style="color: #fff;"><?= $newsItem->detail ?></p>
                                    </div>
                                </td>
                                </tr>
                                </table>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            
            </div>

            <div class="col-md-3 col-sm-6 col-xs-6">
                <div class="dark-transparent-block" style="margin-top: 15px;">
                    <p class="h3 bordered" style="color: #fff; font-weight: 400;">
                    По кол-ву комнат
                    </p>
                    <a class="link-list--item" href="<?= Url::to(['site/search', 'AdvancedFlatSearch[roomsCount][0]' => 1]) ?>" style="text-decoration: none;">
                        <span style="color: #fff;">1-комнатные</span>
                        <span style="color: #fff;">
                            <?= Flat::getWithRooms(1)->onlyActive()->count(); ?>
                        </span>
                    </a>
                    <a class="link-list--item" href="<?= Url::to(['site/search', 'AdvancedFlatSearch[roomsCount][0]' => 2]) ?>" style="text-decoration: none;">
                        <span style="color: #fff;">2-комнатные</span>
                        <span style="color: #fff;">
                            <?= Flat::getWithRooms(2)->onlyActive()->count(); ?>
                        </span>
                    </a>
                    <a class="link-list--item" href="<?= Url::to(['site/search', 'AdvancedFlatSearch[roomsCount][0]' => 3]) ?>" style="text-decoration: none;">
                        <span style="color: #fff;">3-комнатные</span>
                        <span style="color: #fff;">
                            <?= Flat::getWithRooms(3)->onlyActive()->count(); ?>
                        </span>
                    </a>
                    <a class="link-list--item" href="<?= Url::to(['site/search', 'AdvancedFlatSearch[flatType]' => \app\models\search\AdvancedFlatSearch::FLAT_TYPE_STUDIO]) ?>" style="text-decoration: none;">
                        <span style="color: #fff;">Квартиры-студии</span>
                        <span style="color: #fff;">
                            <?= Flat::getStudio()->onlyActive()->count(); ?>
                        </span>
                    </a>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6 col-xs-6">
                <div class="dark-transparent-block" style="margin-top: 15px;">

                    <p class="h3 bordered" style="color: #fff; font-weight: 400;">
                        По сроку сдачи
                    </p>
                    <a class="link-list--item" href="<?= Url::to(['site/search', 'AdvancedFlatSearch[newbuilding_status]' => \app\models\Newbuilding::STATUS_FINISH])?>">
                        <span style="color: #fff;">Сданные</span>
                        <span style="color: #fff;">
                            <?= Flat::getSurrendered()->onlyActive()->count() ?>
                        </span>
                    </a>
                    <a class="link-list--item" href="<?= Url::to(['site/search', 'AdvancedFlatSearch[deadlineYear]' =>  date('Y')])?>">
                        <span style="color: #fff;">До конца года</span>
                        <span style="color: #fff;">
                            <?= Flat::getWithEndYearDeadline()->onlyActive()->count() ?>
                        </span>
                    </a>
                    <a class="link-list--item" href="<?= Url::to(['site/search', 'AdvancedFlatSearch[deadlineYear]' =>  date('Y', strtotime('+1 year'))])?>">
                        <span style="color: #fff;">Сдача в <?= date('Y', strtotime('+1 year')) ?></span>
                        <span style="color: #fff;">
                            <?= Flat::getAfterYearsDeadline(1)->onlyActive()->count() ?>
                        </span>
                    </a>
                    <a class="link-list--item" href="<?= Url::to(['site/search', 'AdvancedFlatSearch[deadlineYear]' =>  date('Y', strtotime('+2 year'))])?>">
                        <span style="color: #fff;">Сдача в <?= date('Y', strtotime('+2 year')) ?> и после</span>
                        <span style="color: #fff;">
                            <?= Flat::getAfterYearsDeadline(2)->onlyActive()->count() ?>
                        </span>
                    </a>

                </div>
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

<!--<section class="agency-block">
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
</section>-->

<!--<section class="bank-block">
    <div class="container">
        <p class="h2">
            Банки
        </p>
    </div>
    <div class="common-slider swiper">
        <?php ListView::widget([
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
</section>-->