<?php
/* @var $model app\models\Flat */
/* @var $this yii\web\View */

use app\components\widgets\FlatsChess;
use app\components\widgets\FloorLayout;
use app\components\widgets\Gallery;
use app\components\widgets\ImageView;
use app\components\widgets\Placemark;
use app\assets\FlatViewAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;

FlatViewAsset::register($this);

$format = \Yii::$app->formatter;

$this->title = $model->roomsTitle . ' №' . $model->number . ', ' . $format->asArea($model->area);
$this->params['breadcrumbs'][] = ['label' => 'Застройщики', 'url' => ['developer/index']];
$this->params['breadcrumbs'][] = ['label' => $model->developer->name, 'url' => ['developer/view', 'id' => $model->developer->id]];
$this->params['breadcrumbs'][] = ['label' => 'Жилые комплексы', 'url' => ['developer/view', 'id' => $model->developer->id]];
$this->params['breadcrumbs'][] = ['label' => $model->newbuildingComplex->name, 'url' => ['newbuilding-complex/view', 'id' => $model->newbuildingComplex->id]];
// $this->params['breadcrumbs'][] = ['label' => 'Позиции', 'url' => ['newbuilding-complex/view', 'id' => $model->newbuildingComplex->id]];
// $this->params['breadcrumbs'][] = ['label' => $model->newbuilding->name, 'url' => ['newbuilding/view', 'id' => $model->newbuilding_id]];
// $this->params['breadcrumbs'][] = ['label' => 'Квартиры', 'url' => ['newbuilding/view', 'id' => $model->newbuilding_id]];
$this->params['breadcrumbs'][] = $model->number;

\yii\web\YiiAsset::register($this);
?>
<div class="row flex-row flat-card">
    <div class="col-xs-12 col-md-8">
        <div class="white-block">
            <h2><?= $model->newbuildingComplex->name ?></h2>
            <p class="lg-text bordered">
                <?= $model->newbuildingComplex->address ?>
            </p>
            <div class="flat-card--properties flex-row">
                <div class="left">
                    <?php if(!is_null($model->newbuildingComplex->developer)): ?>
                        <div class="flat-card--properties__item">
                            <span>Застройщик</span>
                            <?= Html::a($model->newbuildingComplex->developer->name, ['developer/view', 'id' => $model->newbuildingComplex->developer->id]) ?>
                        </div>
                    <?php endif; ?>
                    <?php if(!is_null($model->newbuilding->name)): ?>
                        <div class="flat-card--properties__item">
                            <span>Позиция</span>
                            <?= Html::a($model->newbuilding->name, ['newbuilding/view', 'id' => $model->newbuildingComplex->id]) ?>
                        </div>
                    <?php endif; ?>
                    <?php if(!is_null($model->area)): ?>
                        <div class="flat-card--properties__item">
                            <span>Площадь</span>
                            <b><?= $format->asArea($model->area) ?></b>
                        </div>
                    <?php endif; ?>
                    <?php if(!is_null($model->section)): ?>
                        <div class="flat-card--properties__item">
                            <span>Подъезд</span>
                            <b><?= $model->section ?></b>
                        </div>
                    <?php endif; ?>
                    <?php if(!is_null($model->floor)): ?>
                        <div class="flat-card--properties__item">
                            <span>Этаж</span>
                            <b><?= $format->asFloor($model->floor, $model->newbuilding->total_floor)  ?></b>
                        </div>
                    <?php endif; ?>
                    <?php if(!is_null($model->newbuilding->deadline)): ?>
                        <div class="flat-card--properties__item">
                            <span>Срок сдачи</span>
                            <?php if (strtotime(date("Y-m-d")) > strtotime($model->newbuilding->deadline)): ?>
                                <span class="value"><b>Позиция сдана</b></span>
                            <?php else: ?>
                                <span class="value"><b><?= $format->asQuarterAndYearDate($model->newbuilding->deadline) ?></b></span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="right">
                    <?php if(!is_null($model->newbuildingComplex)): ?>
                        <div class="flat-card--properties__item">
                            <span>Жилой комплекс</span>
                            <?= Html::a($model->newbuildingComplex->name, ['newbuilding-complex/view', 'id' => $model->newbuildingComplex->id]) ?>
                        </div>
                    <?php endif; ?>
                    <?php if(!is_null($model->newbuilding->material)): ?>
                        <div class="flat-card--properties__item">
                            <span>Материал</span>
                            <b><?= $model->newbuilding->material ?></b>
                        </div>
                    <?php endif; ?>
                    <div class="flat-card--properties__item">
                        <span>Отделка</span>
                        <b>
                            <?= $this->render('/common/_furnishes', [
                                'furnishes' => $model->newbuildingComplex->furnishes,
                                'noDataMessage' => 'Нет данных'
                            ]) ?>
                        </b>
                    </div>
                    <div class="flat-card--properties__item">
                        <span>Свободно</span>
                        <b><?= $format->asPercent($model->newbuildingComplex->freeFlats) ?> квартир</b>
                    </div>
                    <?php if(!is_null($model->pricePerArea)): ?>
                        <div class="flat-card--properties__item">
                            <span>Цена за метр</span>
                            <b><?= $format->asPricePerArea($model->pricePerArea) ?></b>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="flat-card--labels">
                <?php if(!is_null($model->newbuildingComplex->minYearlyRate)): ?>
                    <div class="btn btn-red">
                        Ставка от <?= $format->asPercent($model->newbuildingComplex->minYearlyRate) ?>
                    </div>
                <?php endif; ?>

                <!--
                <?php if($model->hasDiscount()): ?>
                    <div class="btn btn-red">
                        Есть скидка
                    </div>
                <?php endif; ?>
                -->

                <?= $this->render('/widgets/flat-price-range', [
                    'model' => $model
                ]) ?>
            </div>

            <?php if($model->layout): ?>
                <!--<div class="flat-card--layout">
                    <?= Html::img(["/uploads/{$model->layout}"], ['id' => 'flat-layout2']) ?>
                </div>-->

                <?= $this->render('/widgets/layouts', [
                    'flat' => $model,
                    'floorLayoutImage' => $floorLayoutImage
                ]) ?>

            <?php endif; ?>



            <?= Gallery::widget([
                'images' => $model->flatImages,
                'fileField' => 'image',
            ]) ?>

            <?php if(!is_null($model->detail) && !empty($model->detail)): ?>
                <p class="h3 bordered">Описание</p>
                <div class="toggle-desc">
                    <div class="toggle-desc--content">
                        <?= $format->asHtml($model->detail) ?>
                    </div>

                    <button class="toggle-desc--trigger"></button>
                </div>
            <?php endif; ?>

            <div class="flat-card--info__mobile">
                <?php if(!is_null($model->newbuildingComplex->advantages)): ?>
                    <?= $this->render('/common/_advantages', [
                        'advantages' => $model->newbuildingComplex->advantages
                    ]);?>
                <?php endif; ?>

                <!--<?= Html::a('Сформировать КП',
                    ['offer/make', 'flatId' => $model->id],
                    ['class' => 'btn btn-red-fill']
                ) ?>-->
            </div>
        </div>
        <?php if($flatDataProvider->totalCount > 0): ?>
            <div class="similar-flat">
                <div class="similar-flat--trigger">
                    <span class="title">Аналогичные квартиры  в других позициях</span>
                    <span class="dropdown-rect"></span>
                </div>
                <div class="similar-flat--content">
                    <?= ListView::widget([
                        'dataProvider' => $flatDataProvider,
                        'itemView' => '/common/_simular-flat-item',
                        'summary' => '',
                        'emptyText' => 'Аналогичные квартиры отсутствуют',
                        'itemOptions' => [
                            'tag' => false,
                        ],
                    ]); ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($model->newbuildingComplex->use_virtual_structure == 1): ?>
            <?= FlatsChess::widget(['use_virtual_structure' => $model->newbuildingComplex->use_virtual_structure, 'newbuildings' => $model->newbuildingComplex->virtualbuildings, 'currentFlat' => $model]) ?>
        <?php else: ?>
            <?= FlatsChess::widget(['newbuildings' => $model->newbuildingComplex->newbuildings, 'currentFlat' => $model]) ?>
        <?php endif; ?>
        
        <?= Placemark::widget([
            'longitude' => $model->newbuildingComplex->longitude,
            'latitude' => $model->newbuildingComplex->latitude,
            'address' => $model->newbuildingComplex->address
        ]) ?>

        <?php if(!is_null($model->newbuildingComplex->stages) && count($model->newbuildingComplex->stages) > 0): ?>
            <div class="white-block interactions">  
                <p class="h3 bordered">Взаимдействие</p>
                <?= $this->render('/common/_stages', [
                    'stages' => $model->newbuildingComplex->stages,
                    'noDataMessage' => 'Нет данных'
                ]) ?>
            </div>
        <?php endif; ?>

        <?php if(!is_null($model->newbuildingComplex->banks) && count($model->newbuildingComplex->banks) > 0): ?>
            <div class="white-block">
                <p class="h3">
                    Аккредитация банков
                </p>
                <?= $this->render('/common/_accreditation', [
                    'flat' => $model,
                    'isEnableCalculation' => true,
                    'banks' => $model->newbuildingComplex->banks,
                    'newbuildingComplex' => $model->newbuildingComplex,
                    'colSizeClass' => 'col-xs-6 col-sm-4',
                    'noDataMessage' => 'Квартира ещё не аккредитована ни в одном банке',
                ]) ?>
            </div>
        <?php endif; ?>

        <?php if(!is_null($model->newbuildingComplex->documents) && count($model->newbuildingComplex->documents) > 0): ?>
            <div class="white-block document-list">
                <p class="h3 bordered">
                    Документация
                </p>
                <div class="document-list--content">
                    <?= $this->render('/common/_documents', [
                        'documents' => $model->newbuildingComplex->documents,
                        'noDataMessage' => 'Нет данных'
                    ])?>
                </div>
                <span class="document-list--trigger"></span>
            </div>
        <?php endif; ?>

        <?php if($newbuildingComplexDataProvider->totalCount > 0): ?>
            <div class="white-block nc-list">
                <p class="h3">
                    Другие ЖК этого застройщика
                </p>
                <br />
                <?= ListView::widget([
                    'dataProvider' => $newbuildingComplexDataProvider,
                    'itemView' => '/common/_newbuilding-complex-item',
                    'summary' => '',
                    'emptyText' => 'Нет данных',
                    'itemOptions' => [
                        'tag' => false,
                    ],
                    'options' => [
                        'class' => 'flex-row row'
                    ]
                ]); ?>
            </div>
        <?php endif; ?>
    </div> 
    <div class="col-md-4 hidden-xs hidden-sm">
        <div class="sticky">
            <div class="sidebar white-block scrollbar">
                <p class="title">
                    <?= $model->roomsTitle . ' №' . $model->number ?>
                    <?php if (!$model->isSold() && (\Yii::$app->user->can('admin') || \Yii::$app->user->can('manager') || \Yii::$app->user->can('agent'))): ?>
                        <span class="favorite btn-favorite 
                        <?php if($model->isFavorite()): ?>
                            in
                        <?php endif; ?>" data-flat-id="<?= $model->id ?>" style="margin-left: 15px;"></span>
                    <?php endif; ?>                    
                </p>

                <p class="updated-at">Обновлено <?= $format->asUpdateDate($model->updated_at) ?></p>
                <?php if(!is_null($model->newbuildingComplex->address)): ?>
                    <p class="address">
                        <?= $model->newbuildingComplex->address ?>
                    </p>
                <?php endif; ?>
                <?php if ($model->is_reserved != 1
                    && $model->status == 0
                    && $model->developer->hasRepresentative()): ?>
                    <?= Html::a('Забронировать',
                        ['reservation/make', 'flatId' => $model->id],
                        ['class' => 'btn btn-red-fill']
                    ) ?>
                <?php endif; ?>
                <?php if ($model->is_reserved != 1 && $model->status == 0): ?>
                    <?= Html::a('КП',
                    ['user/commercial/make', 'flatId' => $model->id],
                    ['class' => 'btn btn-red-fill']) ?>
                <?php endif; ?>
                <div class="title advantages" style="margin-bottom: 10px;">
                    <!--<span>Стоимость</span>-->
                    <span class="value">
                        <?php if($model->hasDiscount()): ?>
                        <?= $format->asCurrencyRange(round($model->allCashPricesWithDiscount[0]['price']), $model->price_cash); ?>    
                        <?php else: ?>
                        <?= $format->asCurrency($model->price_cash); ?>
                        <?php endif; ?>
                    </span>
                </div>
                <div class="info">
                    <?php if(!is_null($model->area)): ?>
                        <div class="info--item">
                            <span class="name">Площадь</span>
                            <span class="value"><?= $format->asArea($model->area) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if(!is_null($model->section)): ?>
                        <div class="info--item">
                            <span class="name">Подъезд</span>
                            <span class="value"><?= $model->section ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if(!is_null($model->floor)): ?>
                        <div class="info--item">
                            <span class="name">Этаж</span>
                            <span class="value"><?= $format->asFloor($model->floor, $model->newbuilding->total_floor) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if(!is_null($model->newbuilding->material)): ?>
                        <div class="info--item">
                            <span class="name">Материал</span>
                            <span class="value"><?= $model->newbuilding->material ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="info--item">
                        <span class="name">Отделка</span>
                        <span class="value">
                            <?= $this->render('/common/_furnishes', [
                                'furnishes' => $model->newbuildingComplex->furnishes,
                                'noDataMessage' => 'Нет данных'
                            ]) ?>
                        </span>
                    </div>
                    <div class="info--item">
                        <span class="name">Свободно</span>
                        <span class="value"><?= $format->asPercent($model->newbuildingComplex->freeFlats) ?> квартир</span>
                    </div>
                </div>
                <?php if(!is_null($model->newbuilding->deadline)): ?>
                    <div class="info--item">
                        <span class="name">Срок сдачи</span>
                        <?php if (strtotime(date("Y-m-d")) > strtotime($model->newbuilding->deadline)): ?>
                            <span class="value"><b>Позиция сдана</b></span>
                        <?php else: ?>
                            <span class="value"><b><?= $format->asQuarterAndYearDate($model->newbuilding->deadline) ?></b></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <?php if(!is_null($model->newbuildingComplex->minYearlyRate)): ?>
                    <div class="btn btn-red">
                        Ставка от <?= $format->asPercent($model->newbuildingComplex->minYearlyRate) ?>
                    </div>
                <?php endif; ?>
                
                <!--
                <?php if($model->hasDiscount()): ?>
                    <div class="btn btn-red">
                        Есть скидка
                    </div>
                <?php endif; ?>
                -->

                <?php if(!is_null($model->newbuildingComplex->advantages) && count($model->newbuildingComplex->advantages) > 0): ?>
                    <?= $this->render('/common/_advantages', [
                        'advantages' => $model->newbuildingComplex->advantages
                    ]);?>
                <?php endif; ?>

                <!--<?= Html::a('Сформировать КП',
                    ['offer/make', 'flatId' => $model->id],
                    ['class' => 'btn btn-red-fill']
                ) ?>-->
                <!--<a href="/reservation">
                    Забронировать
                </a>-->
            </div>
        </div>
    </div>
</div>