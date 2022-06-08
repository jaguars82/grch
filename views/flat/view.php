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
$this->params['breadcrumbs'][] = ['label' => 'Позиции', 'url' => ['newbuilding-complex/view', 'id' => $model->newbuildingComplex->id]];
$this->params['breadcrumbs'][] = ['label' => $model->newbuilding->name, 'url' => ['newbuilding/view', 'id' => $model->newbuilding_id]];
$this->params['breadcrumbs'][] = ['label' => 'Квартиры', 'url' => ['newbuilding/view', 'id' => $model->newbuilding_id]];
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
                            <b><?= $format->asQuarterAndYearDate($model->newbuilding->deadline) ?></b>
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
            <div class="flat-card--labels flex-row">
                <?php if(!is_null($model->newbuildingComplex->minYearlyRate)): ?>
                    <div class="btn btn-red">
                        Ставка от <?= $format->asPercent($model->newbuildingComplex->minYearlyRate) ?>
                    </div>
                <?php endif; ?>

                <?php if($model->hasDiscount()): ?>
                    <div class="btn btn-red">
                        Действует скидка - <?= $format->asPercent($model->discount) ?>
                    </div>
                <?php endif; ?>

                <div class="price-block">
                    <span>Стоимость</span>
                    <span class="value">
                        <?php if($model->hasDiscount()): ?>
                        <?= $format->asCurrency($model->cashPriceWithDiscount); ?>    
                        <?php else: ?>
                        <?= $format->asCurrency($model->price_cash); ?>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
            <?php if($model->layout): ?>
                <div class="flat-card--layout">
                    <div id="" style="position: relative;">
                    <?= $this->render('/widgets/compass-rose', [
                        'id' => 'compass-rose-flat',
                        'azimuth' => $model->entrance->azimuth
                    ]) ?>
                    </div>
                    <?= Html::img(["/uploads/{$model->layout}"], ['id' => 'flat-layout']) ?>
                </div>
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

                <?= Html::a('Сформировать КП',
                    ['offer/make', 'flatId' => $model->id],
                    ['class' => 'btn btn-red-fill']
                ) ?>
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
        
        <?= FlatsChess::widget(['newbuildings' => $model->newbuildingComplex->newbuildings, 'currentFlat' => $model]) ?>

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
                </p>
                <p class="updated-at">Обновлено <?= $format->asUpdateDate($model->updated_at) ?></p>
                <?php if(!is_null($model->newbuildingComplex->address)): ?>
                    <p class="address">
                        <?= $model->newbuildingComplex->address ?>
                    </p>
                <?php endif; ?>
                <div class="price-block">
                    <span>Стоимость</span>
                    <span class="value">
                        <?php if($model->hasDiscount()): ?>
                        <?= $format->asCurrency($model->cashPriceWithDiscount); ?>    
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
                <?php if($floorLayoutImage): ?>
                    <div class="floor-layout">
                        <?= $floorLayoutImage ?>
                    </div>
                    <div id="floor-layout" class="hidden">
                        <div class="modal-buttons">
                            <span id="close-modal-window" class="material-icons-outlined">close</span>
                        </div>
                        <?= $this->render('/widgets/compass-rose', [
                            'id' => 'compass-rose-entrance',
                            'azimuth' => $model->entrance->azimuth
                        ]) ?>
                        <div class="image-container">
                            <div id="entrance-layout" style="width: 600px; max-width: 600px;">
                                <?= $floorLayoutImage ?>
                            </div>
                        </div>
                    </div>
                    <div style="display: flex; justify-content: end;">
                    <?= $this->render('/widgets/controls/button', [
                        'button_id' => 'expand-floor-layout',
                        'icon' => 'fullscreen',
                        'class' => 'simple',
                        'wrapper_style' => 'margin-top: -25px; margin-bottom: 15px;'
                    ]) ?>
                    </div>
                    <?= $this->render('/widgets/modal-window', [
                        'modal_id' => 'floor-layout',
                        'content' => $floorLayoutImage,
                    ]) ?>
                <?php endif; ?>
                <?php if(!is_null($model->newbuilding->deadline)): ?>
                    <div class="deadline-block">
                        <span>Срок сдачи</span>
                        <span><b><?= $format->asQuarterAndYearDate($model->newbuilding->deadline) ?></b></span>
                    </div>
                <?php endif; ?>
                
                <?php if(!is_null($model->newbuildingComplex->minYearlyRate)): ?>
                    <div class="btn btn-red">
                        Ставка от <?= $format->asPercent($model->newbuildingComplex->minYearlyRate) ?>
                    </div>
                <?php endif; ?>
                
                <?php if($model->hasDiscount()): ?>
                    <div class="btn btn-red">
                        Действует скидка - <?= $format->asPercent($model->discount) ?>
                    </div>
                <?php endif; ?>

                <?php if(!is_null($model->newbuildingComplex->advantages) && count($model->newbuildingComplex->advantages) > 0): ?>
                    <?= $this->render('/common/_advantages', [
                        'advantages' => $model->newbuildingComplex->advantages
                    ]);?>
                <?php endif; ?>

                <?= Html::a('Сформировать КП',
                    ['offer/make', 'flatId' => $model->id],
                    ['class' => 'btn btn-red-fill']
                ) ?>
            </div>
        </div>
    </div>
</div>