<?php
use app\assets\OfferMakeAsset;
use app\components\widgets\Placemark;
use yii\helpers\Html;
use yii\widgets\DetailView;
use app\components\flat\SvgDom;
use app\components\offer\SvgImage;


$format = \Yii::$app->formatter;

// $title = $flat->roomsTitle . ' №' . $flat->number . ', ' . $format->asArea($flat->area);

/*foreach ($flats as $flat) {
    $flat->imagePath = isset($path) ? $path : '';
    $flat->floorLayoutPath = isset($floorLayout)
        ? $floorLayout
        : (
        !is_null($flat->floor_layout) && !empty($flat->floor_layout)
            ? "$flat->imagePath/uploads/{$flat->floor_layout}"
            : (
        !is_null($flat->floorLayout)
            ? "$flat->imagePath/uploads/{$flat->floorLayout->image}"
            : ''
        )
    );
}*/

if (isset($isView)) {
    if (isset($offer)) {
        $priceCache = $offer->new_price_cash;
        $priceCredit = $offer->new_price_credit;
    } else {
        $priceCache = $flat->price_cash;
        /*if($flat->hasDiscount()) {
            $priceCache = $flat->cashPriceWithDiscount; 
        } else {
            $priceCache = $flat->price_cash;
        }*/
        $priceCredit = $flat->price_credit;
    }

    if (isset($newPriceCash)) {
        $priceCache = $newPriceCash;
    }

    if (isset($newPriceCredit)) {
        $priceCredit = $newPriceCredit;
    }
}

/*if (isset($flat)) {
    // $priceCachePrint = $flat->price_cash;
    if($flat->hasDiscount()) {
        $priceCachePrint = $flat->cashPriceWithDiscount; 
    } else {
        $priceCachePrint = $flat->price_cash;
    }
    $priceCreditPrint = $flat->price_credit;
}*/

OfferMakeAsset::register($this);
$this->registerCssFile('/css/offer-print.css', ['media' => 'print']);
$this->registerCssFile('/css/offer.css', ['media' => 'print']);

$settings = json_decode($settings);

if(count($flats) > 1) {
    $commercialMode = 'multiple';
    $chunkedFlats = array_chunk($flats, 5);
}

?>

<?php if(isset($commercialMode) && $commercialMode === 'multiple' && $settings->compareTable === true): ?>

    <div class="gray-bg">
        <p class="no-public-offert">не является публичной офертой</p>
        <p class="commercial-offer-title">
            Коммерческое предложение № <?= $commercial->number ?>
        </p>
    </div>

    <!-- initiator (user) info on 1st page (above comparison table) -->
    <?php if(!\Yii::$app->user->isGuest): ?>
        <div class="white-bg">
            <table class="contact-table" style="width: 100%;">
                <tr>
                    <td style="width: 100px;">
                        <div class="agency-image">
                            <?php if(
                                !is_null($user->agency)
                                && !is_null($user->agency->logo)
                            ): ?>
                                <img src="@web/uploads/<?= $user->agency->logo ?>" style="width: 85px; max-width: 85px;" />
                                <!--<?= Html::img(Yii::getAlias("/uploads/{$user->agency->logo}")) ?>-->
                            <?php else: ?>
                                <!--<?= Html::img(Yii::getAlias("@web/img/office.png")) ?>-->
                                <img src="/img/office.png" style="width: 85px; max-width: 85px;" />
                            <?php endif ?>
                        </div>
                    </td>
                    <td style="text-align: right;">
                        <div style="width: 100%; text-align: right;">
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
                        <div style="width: 100%; text-align: right;">
                        <a href="tel:<?= $user->phone ?>" class="phone">
                            <span class="icon">
                                <?= Html::img(Yii::getAlias('@web/img/icons/phone.png'));?>
                            </span>
                            <?= $user->phone ?>
                        </a>
                        </div>
                        <?php endif; ?>
                        <?php if(!is_null($user->email)): ?>
                        <div style="width: 100%; text-align: right;">
                        <a href="mailto:<?= $user->email ?>" class="email">
                            <span class="icon">
                                <?= Html::img(Yii::getAlias('@web/img/icons/mail.png'));?>
                            </span>
                            <?= $user->email ?>
                        </a>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td style="width: 100px;">
                        <?php if(!is_null($user->photo)): ?>
                            <img src="/uploads/<?= $user->photo ?>" style="width: 85px; height: 85px; border-radius: 50px;" />
                            <!--<?= Html::img(\Yii::getAlias("@web/uploads/{$user->photo}")) ?>-->
                        <?php else: ?>
                            <img src="/img/blank-person.png" style="width: 85px; height: 85x; border-radius: 50px;" />
                            <!--<?= Html::img(\Yii::getAlias("@web/img/blank-person.png")); ?>-->
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>
    <?php endif;?>
    <!-- END OF initiator (user) info on 1st page (above comparison table) -->

    <!-- comparison table -->
    <table class="compare-table">
        <colgroup>
            <col span="1" style="width: 150px;">
            <col span="1">
            <col span="1">
            <col span="1">
            <col span="1">
            <col span="1">
            <col span="1">
            <col span="1">
        </colgroup>
        <tbody>
            <tr style="width: 170px;">
                <td class="compare-table-rowtitle" style="width: 170px;">Планировка</td>
                <td class="compare-table-rowtitle">Цена</td>
                <td class="compare-table-rowtitle">Площадь</td>
                <td class="compare-table-rowtitle">Тип</td>
                <td class="compare-table-rowtitle">Этаж</td>
                <td class="compare-table-rowtitle">Сдача</td>
                <td class="compare-table-rowtitle">Застройщик</td>
                <td class="compare-table-rowtitle">ЖК</td>
            </tr>
            <?php foreach ($flats as $flat): ?>
                <tr class="compare-table-row">
                    <td class="compare-table-cell layout" style="text-align: left;">
                        <table style="width: 200px;  max-width: 200px;">
                            <tr>
                                <td style="width: 30%;">
                                    <?php if($flat->layout): ?>
                                        <img class="compare-table-layout" src="/uploads/<?= $flat->layout ?>" style="width: 150px; max-width: 150px;" />
                                    <?php else: ?>
                                        <img class="compare-table-layout" src="/img/blank.png" />
                                    <?php endif; ?>
                                </td>
                                <td style="width: 70%;">
                                    <span><?= $flat->newbuildingComplex->name ?></span>
                                    <span> > <?= $flat->newbuilding->name ?></span>
                                    <span> > <?= $flat->entrance->name ?></span>
                                    <span> > № <?=$flat->number?></span>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td class="compare-table-cell"><strong><?= $format->asCurrency($flat->price_cash) ?></strong></td>
                    <td class="compare-table-cell"><?= $format->asArea($flat->area) ?></td>
                    <td class="compare-table-cell">
                        <span><?= $flat->rooms ?></span>
                        <?php if($flat->rooms > 0 && $flat->rooms < 2): ?>
                        <span>-но</span>
                        <?php elseif($flat->rooms >= 2 && $flat->rooms < 5): ?>
                        <span>-х</span>
                        <?php else: ?>
                        <span>-и</span>
                        <?php endif; ?>
                        <span> комнатная</span>
                        <?php if($flat->is_studio): ?>
                        <span> студия</span>
                        <?php else: ?>
                        <span> квартира</span>
                        <?php endif; ?>
                    </td>
                    <td class="compare-table-cell"><?= $flat->floor ?></td>
                    <td class="compare-table-cell">
                        <?php if ($flat->newbuilding->deadline): ?>
                            <?php if (strtotime(date("Y-m-d")) > strtotime($flat->newbuilding->deadline)): ?>
                            позиция сдана
                            <?php else: ?>
                                <?= $format->asQuarterAndYearDate($flat->newbuilding->deadline) ?>
                            <?php endif; ?>
                        <?php else: ?>
                            <span>нет данных</span>
                        <?php endif; ?>
                    </td>
                    <td class="compare-table-cell"><?= $flat->developer->name ?></td>
                    <td class="compare-table-cell"><?= $flat->newbuildingComplex->name ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <!-- END OF comparison table -->

<?php endif; ?>

<?php foreach($flats as $flat): ?>
<div class="commercial-offer">
    <div class="page">

        <div class="gray-bg">
            <p class="no-public-offert">не является публичной офертой</p>
            <p class="commercial-offer-title">
                Коммерческое предложение № <?= $commercial->number ?>
            </p>
        </div>

        <?php if(!\Yii::$app->user->isGuest): ?>
        <div class="white-bg">
            <table class="contact-table" style="width: 100%;">
                <tr>
                    <td style="width: 100px;">
                        <div class="agency-image">
                            <?php if(
                                !is_null($user->agency)
                                && !is_null($user->agency->logo)
                            ): ?>
                                <img src="@web/uploads/<?= $user->agency->logo ?>" style="width: 85px; max-width: 85px;" />
                                <!--<?= Html::img(Yii::getAlias("/uploads/{$user->agency->logo}")) ?>-->
                            <?php else: ?>
                                <!--<?= Html::img(Yii::getAlias("@web/img/office.png")) ?>-->
                                <img src="/img/office.png" style="width: 85px; max-width: 85px;" />
                            <?php endif ?>
                        </div>
                    </td>
                    <td style="text-align: right;">
                        <div style="width: 100%; text-align: right;">
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
                        <div style="width: 100%; text-align: right;">
                        <a href="tel:<?= $user->phone ?>" class="phone">
                            <span class="icon">
                                <?= Html::img(Yii::getAlias('@web/img/icons/phone.png'));?>
                            </span>
                            <?= $user->phone ?>
                        </a>
                        </div>
                        <?php endif; ?>
                        <?php if(!is_null($user->email)): ?>
                        <div style="width: 100%; text-align: right;">
                        <a href="mailto:<?= $user->email ?>" class="email">
                            <span class="icon">
                                <?= Html::img(Yii::getAlias('@web/img/icons/mail.png'));?>
                            </span>
                            <?= $user->email ?>
                        </a>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td style="width: 100px;">
                        <?php if(!is_null($user->photo)): ?>
                            <img src="/uploads/<?= $user->photo ?>" style="width: 85px; height: 85px; border-radius: 50px;" />
                            <!--<?= Html::img(\Yii::getAlias("@web/uploads/{$user->photo}")) ?>-->
                        <?php else: ?>
                            <img src="/img/blank-person.png" style="width: 85px; height: 85x; border-radius: 50px;" />
                            <!--<?= Html::img(\Yii::getAlias("@web/img/blank-person.png")); ?>-->
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>
        <?php endif;?>

        <div class="gray-bg">
            <table style="width:100%;">
                <tr>
                    <td style="width: 75%;">
                        <p class="commercial-offer-title">
                            <?= $flat->roomsTitle . ' №' . $flat->number . ', ' . $format->asArea($flat->area) ?>
                        </p>
                            <?= $flat->newbuilding->newbuildingComplex->name.' > '.$flat->newbuilding->name. ' > '.$flat->floor.'-й этаж' ?>
                            <?php if (!empty($flat->newbuilding->total_floor)): ?>
                                <?= ' (из '.$flat->newbuilding->total_floor.')' ?>
                            <?php endif; ?>
                                сдача 
                            <?php if (strtotime(date("Y-m-d")) > strtotime($flat->newbuilding->deadline)): ?>
                                позиция сдана
                            <?php else: ?>
                                <?= $format->asQuarterAndYearDate($flat->newbuilding->deadline) ?>
                            <?php endif; ?>
                        </p>
                    </td>
                    <td style="width: 25%; text-align: right;">
                        <p style="font-size: 24px; fon-weight: 600; color: #5197f7; margin: 5px 0;">
                            <?= $format->asCurrency($flat->price_cash) ?>
                        </p>
                        <p style="margin: 5px 0;">
                            <b><?= $format->asPricePerArea($flat->pricePerArea) ?></b>
                        </p>
                    </td>
                </tr>
            </table>
        </div>

        <!--<div class="white-bg">
            <p style="font-size: 24px; fon-weight: 600; color: #5197f7; margin: 5px 0;">
                <?= $format->asCurrency($flat->price_cash) ?>
            </p>
            <p style="margin: 5px 0;">
                <b><?= $format->asPricePerArea($flat->pricePerArea) ?></b>
            </p>
        </div>-->

        <!--
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
        -->

        <?php if(!is_null($flat->layout) || !is_null($flat->floorLayout)): ?>
        <div class="white-bg" style="height: 200px; max-height: 200px; overflow: hidden;">
            <div class="row" style="height: 200px; max-height: 200px; overflow: hidden;">
                <?php if(!is_null($flat->layout)): ?>
                    <div class="col-xs-6" style="height: 200px; max-height: 200px; overflow: hidden;">
                        <div class="image-holder">
                            <p class="layout-title">
                                Планировка квартиры
                            </p>
                            <p class="center layout-img">
                                <?php if(SvgDom::isNameSvg($flat->layout)): ?>
                                    <img src="/uploads/<?= $flat->layout ?>" class="flat-image" />
                                <?php else: ?>
                                    <img src="/uploads/<?= $flat->layout ?>" style="flat-image" />
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                <?php endif ?>
                <?php if(!is_null($flat->floorLayout)): ?>
                    <div class="col-xs-6" style="height: 200px; max-height: 200px; overflow: hidden;">
                        <div class="image-holder">
                            <p class="layout-title">
                                Поэтажный план
                            </p>
                            <div class="center layout-img" style="height: 200px; max-height: 200px; overflow: hidden;">
                            <?php if(SvgDom::isNameSvg($flat->floorLayout->image)): ?>
                                <img src="/uploads/floorlayout-selections/<?= $flat->floorLayout->image ?>" class="floor-image" />
                            <?php else: ?>
                                <!--<?= Html::img([$flat->floorLayoutPath]) ?>-->
                                <img src="<?= $flat->floorLayoutPath ?>" style="height: 200px; max-height: 200px;" />
                            <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php
        $hasCoords = !is_null($flat->newbuildingComplex->latitude) && !is_null($flat->newbuildingComplex->longitude) && !empty($flat->newbuildingComplex->latitude) && !empty($flat->newbuildingComplex->longitude);
        if($hasCoords || !is_null($flat->newbuildingComplex->address)): ?>
            <div class="gray-bg">
            <p class="commercial-offer-title">
                <span>На карте</span>
            </p>
                <?php if(!is_null($flat->newbuildingComplex->address)): ?>
                    <span>
                        <?= $flat->newbuildingComplex->address ?>
                    </span>
                <?php endif; ?>
            </div>
            <div class="address-inline">
                <!--<div class="address-inline-info">
                    <span class="address-inline-info--title"> На карте </span>
                    <?php if(!is_null($flat->newbuildingComplex->address)): ?>
                    <span class="address-inline-info--text">
                        <?= $flat->newbuildingComplex->address ?>
                    </span>
                    <?php endif; ?>
                </div>-->
                <?php if($hasCoords): ?>  
                    <div class="address-inline-map">
                        <?php if(isset($isPlacemarkImage) && $hasCoords): ?>
                            <?= Html::img("https://static-maps.yandex.ru/1.x/?ll={$flat->newbuildingComplex->latitude},{$flat->newbuildingComplex->longitude}&size=650,280&z=16&l=map&pt={$flat->newbuildingComplex->latitude},{$flat->newbuildingComplex->longitude},pm2vvm") ?>
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


        <?php if(!is_null($flat->newbuildingComplex->advantages) && count($flat->newbuildingComplex->advantages) > 0): ?>
        <div class="gray-bg" style="margin-top: 10px;">
            <p class="commercial-offer-title">
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
                                    <!--<?= SvgImage::get(\Yii::getAlias("@webroot/uploads/{$advantage->icon}")) ?>-->
                                    <img src="/uploads/<?= $advantage->icon ?>" style="width: 30px; height: 30px;" />
                                <?php else: ?>
                                    <!--<?= Html::img("/uploads/{$advantage->icon}")?>-->
                                    <img src="/uploads/<?= $advantage->icon ?>" style="width: 30px; height: 30px;" />
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

    </div>

</div>
<?php endforeach; ?>