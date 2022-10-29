<?php
use app\assets\OfferMakeAsset;
use app\components\widgets\Placemark;
use yii\helpers\Html;
use yii\widgets\DetailView;
use app\components\flat\SvgDom;
use app\components\offer\SvgImage;


$format = \Yii::$app->formatter;
$title = $flat->roomsTitle . ' №' . $flat->number . ', ' . $format->asArea($flat->area);

$imagePath = isset($path) ? $path : '';
$floorLayoutPath = isset($floorLayout)
    ? $floorLayout
    : (
    !is_null($flat->floor_layout) && !empty($flat->floor_layout)
        ? "$imagePath/uploads/{$flat->floor_layout}"
        : (
    !is_null($flat->floorLayout)
        ? "$imagePath/uploads/{$flat->floorLayout->image}"
        : ''
    )
);

if (isset($isView)) {
    if (isset($offer)) {
        $priceCache = $offer->new_price_cash;
        $priceCredit = $offer->new_price_credit;
    } else {
        // $priceCache = $flat->price_cash;
        if($flat->hasDiscount()) {
            $priceCache = $flat->cashPriceWithDiscount; 
        } else {
            $priceCache = $flat->price_cash;
        }
        $priceCredit = $flat->price_credit;
    }

    if (isset($newPriceCash)) {
        $priceCache = $newPriceCash;
    }

    if (isset($newPriceCredit)) {
        $priceCredit = $newPriceCredit;
    }
}

if (isset($flat)) {
    // $priceCachePrint = $flat->price_cash;
    if($flat->hasDiscount()) {
        $priceCachePrint = $flat->cashPriceWithDiscount; 
    } else {
        $priceCachePrint = $flat->price_cash;
    }
    $priceCreditPrint = $flat->price_credit;
}

OfferMakeAsset::register($this);
$this->registerCssFile('/css/offer-print.css', ['media' => 'print']);
$this->registerCssFile('/css/offer.css', ['media' => 'print']);
?>

<div class="commercial-offer">
    <div class="page">
        <div class="gray-bg">
            <p class="commercial-offer-title">
                <?= $title ?>
            </p>
            <?php if(!is_null($flat->newbuilding->address) && !empty($flat->newbuilding->address) ||
                    !is_null($flat->newbuildingComplex->address) && !empty($flat->newbuildingComplex->address)): ?>
                <p class="commercial-offer-address">
                    <?= !empty($flat->newbuilding->address) ? $flat->newbuilding->address : $flat->newbuildingComplex->address ?>
                </p>
            <?php endif ?>
        </div>

        <?php if(!\Yii::$app->user->isGuest): ?>
        <div class="white-bg">
            <div class="flex-row">
                <div class="person">
                    <div class="image">
                        <?php if(!is_null($user->photo)): ?>
                            <?= Html::img(\Yii::getAlias("@web/uploads/{$user->photo}")) ?>
                        <?php else: ?>
                            <?= Html::img(\Yii::getAlias("@web/img/blank-person.png")); ?>
                        <?php endif; ?>
                    </div>
                    <div class="content">
                        <div class="flex-row">
                            <?php if(!is_null($user->roleLabel)): ?>
                            <b>
                                <?= $user->roleLabel ?>
                            </b>
                            <?php endif; ?>
                            <span class="name">
                                <?= $user->fullName ?>
                            </span>
                        </div>
                        <?php if(!is_null($user->phone)): ?>
                        <a href="tel:<?= $user->phone ?>" class="phone">
                            <span class="icon">
                                <?= Html::img(Yii::getAlias('@web/img/icons/phone.png'));?>
                            </span>
                            <?= $user->phone ?>
                        </a>
                        <?php endif; ?>
                        <?php if(!is_null($user->email)): ?>
                        <a href="mailto:<?= $user->email ?>" class="email">
                            <span class="icon">
                                <?= Html::img(Yii::getAlias('@web/img/icons/mail.png'));?>
                            </span>
                            <?= $user->email ?>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="agency-image">
                    <?php if(
                        !is_null($user->agency)
                        && !is_null($user->agency->logo)
                    ): ?>
                        <?= Html::img(Yii::getAlias("@web/uploads/{$user->agency->logo}")) ?>
                    <?php else: ?>
                        <?= Html::img(Yii::getAlias("@web/img/office.png")) ?>
                    <?php endif ?>
                </div>
            </div>
        </div>
        <?php endif;?>

        <div class="gray-bg">
            <div class="row info">
                <div class="col-xs-6">
                    <div class="info-item">
                        <span>Застройщик</span>
                        <?= Html::a($format->asCapitalize($flat->developer->name), ['developer/view', 'id' => $flat->developer->id])?>
                    </div>
                    <?php if(!is_null($flat->area)): ?>
                    <div class="info-item">
                        <span>Площадь</span>
                        <b><?= $format->asArea($flat->area) ?></b>
                    </div>
                    <?php endif; ?>
                    <?php if(!is_null($flat->section)): ?>
                    <div class="info-item">
                        <span>Подъезд</span>
                        <b><?= $flat->section ?></b>
                    </div>
                    <?php endif; ?>
                    <?php if(!is_null($flat->floor)): ?>
                        <div class="info-item">
                            <span>Этаж</span>
                            <b><?= $format->asFloor($flat->floor, $flat->newbuilding->total_floor)  ?></b>
                        </div>
                    <?php endif; ?>

                    <?php if(!is_null($flat->newbuilding->deadline)): ?>
                        <div class="info-item">
                            <span>Срок сдачи</span>
                            <?php if (strtotime(date("Y-m-d")) > strtotime($flat->newbuilding->deadline)): ?>
                                <b>Позиция сдана</b>
                            <?php else: ?>
                                <b><?= $format->asQuarterAndYearDate($flat->newbuilding->deadline) ?></b>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-xs-6">
                    <div class="info-item">
                        <span>Жилой комплекс</span>
                        <?= Html::a($format->asCapitalize($flat->newbuildingComplex->name), ['newbuilding-complex/view', 'id' => $flat->newbuildingComplex->id]) ?>
                    </div>

                    <?php if(!is_null($flat->newbuilding->material)): ?>
                        <div class="info-item">
                            <span>Материал</span>
                            <b><?= $flat->newbuilding->material ?></b>
                        </div>
                    <?php endif; ?>

                    <div class="info-item">
                        <span>Отделка</span>
                        <b>
                            <?= $this->render('/common/_furnishes', [
                                'furnishes' => $flat->newbuildingComplex->furnishes,
                                'withoutLinks' => true,
                                'noDataMessage' => 'Нет данных'
                            ]) ?>
                        </b>
                    </div>

                    <div class="info-item">
                        <span>Свободно</span>
                        <b><?= $format->asPercent($flat->newbuildingComplex->freeFlats) ?> квартир</b>
                    </div>

                    <?php if(!is_null($flat->pricePerArea)): ?>
                        <div class="info-item">
                            <span>Цена за метр</span>
                            <b><?= $format->asPricePerArea($flat->pricePerArea) ?></b>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-xs-12">
                    <?php if(!is_null($flat->newbuildingComplex->minYearlyRate)): ?>
                        <div class="btn btn-red">
                            Ставка от <?= $format->asPercent($flat->newbuildingComplex->minYearlyRate) ?>
                        </div>
                    <?php endif; ?>

                    <?php if($flat->hasDiscount()): ?>
                    <div class="btn btn-red">
                        Есть скидка
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if(!is_null($flat->layout) || !is_null($flat->floorLayout)): ?>
        <div class="white-bg">
            <div class="row">
                <?php if(!is_null($flat->layout)): ?>
                    <div class="col-xs-6">
                        <p class="layout-title">
                            Планировка квартиры
                        </p>
                        <p class="center layout-img">
                            <?php if(SvgDom::isNameSvg($flat->layout)): ?>
                                <?= SvgImage::get(\Yii::getAlias("@webroot/uploads/{$flat->layout}")) ?>
                            <?php else: ?>
                                <?= Html::img(["$imagePath/uploads/{$flat->layout}"]) ?>
                            <?php endif; ?>
                        </p>
                    </div>
                <?php endif ?>
                <?php if(!is_null($flat->floorLayout)): ?>
                    <div class="col-xs-6">
                        <p class="layout-title">
                            Поэтажный план
                        </p>
                        <p class="center layout-img">
                            <?php if(SvgDom::isNameSvg($flat->floorLayout->image)): ?>
                                <?= SvgImage::get(\Yii::getAlias("@webroot/uploads/{$flat->floorLayout->image}")) ?>
                            <?php else: ?>
                                <?= Html::img([$floorLayoutPath]) ?>
                            <?php endif; ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="gray-bg">
            <p class="price">
                <?= $this->render('/common/_price', [
                    'condition' => false,
                    'onePrice' => $priceCachePrint,
                ]) ?>
            </p>
        </div>
    </div>
              
    <?php
    $hasCoords = !is_null($flat->newbuildingComplex->latitude) && !is_null($flat->newbuildingComplex->longitude) && !empty($flat->newbuildingComplex->latitude) && !empty($flat->newbuildingComplex->longitude);
    if($hasCoords || !is_null($flat->newbuildingComplex->address)): ?>
        <div class="address-inline">
            <div class="address-inline-info">
                <span class="address-inline-info--title"> На карте </span>
                <?php if(!is_null($flat->newbuildingComplex->address)): ?>
                <span class="address-inline-info--text">
                    <?= $flat->newbuildingComplex->address ?>
                </span>
                <?php endif; ?>
            </div>
            <?php if($hasCoords): ?>  
                <div class="address-inline-map">
                    <?php if(isset($isPlacemarkImage) && $hasCoords): ?>
                        <?= Html::img("https://static-maps.yandex.ru/1.x/?ll={$flat->newbuildingComplex->latitude},{$flat->newbuildingComplex->longitude}&size=650,379&z=16&l=map&pt={$flat->newbuildingComplex->latitude},{$flat->newbuildingComplex->longitude},pm2vvm") ?>
                    <?php else: ?>
                        <?= Placemark::widget([
                            'longitude' => $flat->newbuildingComplex->longitude,
                            'latitude' => $flat->newbuildingComplex->latitude,
                            'inline' => true
                        ]) ?>
                    <?php endif ?>
                </div>
            <?php endif ?>
        </div>
    <?php endif ?>

    <?php if(!is_null($flat->newbuildingComplex->advantages)): ?>
        <div class="gray-bg advantages-row">
            <p class="advantages-row--title">
                Преимущества
            </p>
                <table style="width: 100%;">
                <?php 
                $count = 0;
                foreach($flat->newbuildingComplex->advantages as $advantage): 
                    if(is_null($advantage->icon)):
                        continue;
                    endif;
                ?>
                    <?php if($count % 3 == 0) :?>
                    <tr>
                    <?php endif; ?>
                        <td style="width: 30%;">
                            <span class="icon">
                                <?php if(SvgDom::isNameSvg($advantage->icon)): ?>
                                    <?= SvgImage::get(\Yii::getAlias("@webroot/uploads/{$advantage->icon}")) ?>
                                <?php else: ?>
                                    <?= Html::img("$imagePath/uploads/{$advantage->icon}")?>
                                <?php endif; ?>
                            </span>
                            <?= $advantage->name ?>
                        </td>
                    <?php if (($count > 0 && $count % 3 == 2) || (($count + 1) == count($flat->newbuildingComplex->advantages))) :?>
                    </tr>
                    <?php endif; ?>

                    <?php ++$count ?>
                <?php endforeach; ?>
                </table>
        </div>
    <?php endif; ?>

    <div class="collapse <?php if(isset($settings) && isset($settings['newbuilding_complex']) && $settings['newbuilding_complex']): ?>in<?php endif ?>" id="newbuilding-complex-info">
        <div class="white-bg">
            <h3><?= $flat->newbuildingComplex->name ?></h3>
        </div>
            
        <div class="gray-bg">

            <?php if(!is_null($flat->newbuildingComplex->logo)): ?>
                <div style="margin-top: 15px; margin-bottom: 15px">
                    <?= Html::img(["$imagePath/uploads/{$flat->newbuildingComplex->logo}"], ['height' => 80]) ?>
                </div>
            <?php endif ?>

            <?php if(!is_null($flat->newbuildingComplex->address) && !empty($flat->newbuildingComplex->address)): ?>
                <p><?= $flat->newbuildingComplex->address ?></p>
            <?php endif ?>

            <?php if(!is_null($flat->newbuildingComplex->district) && !empty($flat->newbuildingComplex->district->name)): ?>
                <p><?= $format->asDistrict($flat->newbuildingComplex->district->name) ?></p>
            <?php endif ?>

            <?php if(!is_null($flat->newbuildingComplex->detail) && !empty($flat->newbuildingComplex->detail)): ?>
                <p class="text-justify">
                    <?= $format->asHtml($flat->newbuildingComplex->detail) ?>
                </p>
            <?php endif ?>

            <?php if(!is_null($flat->newbuildingComplex->offer_info) && !empty($flat->newbuildingComplex->offer_info)): ?>
                <p class="text-justify">
                    <?= $format->asHtml($flat->newbuildingComplex->offer_info) ?>
                </p>
            <?php endif ?>
        </div>
    </div>

    <div class="collapse <?php if(isset($settings) && isset($settings['furnishes']) && $settings['furnishes']): ?>in<?php endif ?>" id="furnishes-info">
        <div class="furnish-block">
            <?php foreach($flat->furnishes as $key => $furnish): ?>
                <div class="white-bg">
                    <p class="furnish-block--title">
                        <?= $furnish->name ?>
                    </p>
                </div>
                <div class="gray-bg clearfix">
                        <div class="desc">
                            <div class="inner">
                                <p>
                                    <?= $furnish->detail ?>
                                </p>
                            </div>
                        </div>
                        <div class="image">
                            <?php if(!is_null($furnish->furnishImages) && ($furnishImage = $furnish->furnishImages[0])): ?>
                                <?= Html::img("$imagePath/uploads/{$furnishImage->image}") ?>
                            <?php endif; ?>
                        </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

        <div class="collapse <?php if(isset($settings) && isset($settings['developer']) && $settings['developer']): ?>in<?php endif ?>" id="developer-info">
            <div class="white-bg">
                <h3><?= $flat->developer->name ?></h3>
            </div>
            <div class="gray-bg">

                <?php if(!is_null($flat->developer->logo)): ?>
                    <div style="margin-top: 15px; margin-bottom: 15px">
                        <?= Html::img(["$imagePath/uploads/{$flat->developer->logo}"], ['height' => 80]) ?>
                    </div>
                <?php endif ?>

                <?php if(!is_null($flat->developer->address) && !empty($flat->developer->address)): ?>
                    <p><?= $flat->developer->address ?></p>
                <?php endif ?>

                <?php if(!is_null($flat->developer->detail) && !empty($flat->developer->detail)): ?>
                    <p class="text-justify">
                        <?= $format->asHtml($flat->developer->detail) ?>
                    </p>
                <?php endif ?>

                <?php if(!is_null($flat->developer->offer_info) && !empty($flat->developer->offer_info)): ?>
                    <p class="text-justify">
                        <?= $format->asHtml($flat->developer->offer_info) ?>
                    </p>
                <?php endif ?>

                <?php if(!is_null($flat->developer->free_reservation) && !empty($flat->developer->free_reservation)): ?>
                    <p>
                    <h4>Условия бесплатной брони</h4>
                    <p class="text-justify">
                        <?= $format->asHtml($flat->developer->free_reservation) ?>
                    </p>
                    </p>
                <?php endif ?>

                <?php if(!is_null($flat->developer->paid_reservation) && !empty($flat->developer->paid_reservation)): ?>
                    <p>
                    <h4>Условия платной брони</h4>
                    <p class="text-justify">
                        <?= $format->asHtml($flat->developer->paid_reservation) ?>
                    </p>
                    </p>
                <?php endif ?>
            </div>
        </div>
</div>