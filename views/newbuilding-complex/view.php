<?php
/* @var $model app\models\NewbuildingComplex */
/* @var $this yii\web\View */

use app\assets\NewbuildingComplexViewAsset;
use app\components\widgets\ImageView;
use app\components\widgets\FileInputButton;
use app\components\widgets\FlatsChess;
use app\components\widgets\Placemark;
use app\components\widgets\Gallery;
use app\models\Newbuilding;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;
use yii\widgets\Pjax;

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Застройщики', 'url' => ['developer/index']];
$this->params['breadcrumbs'][] = ['label' => $model->developer->name, 'url' => ['developer/view', 'id' => $model->developer->id]];
$this->params['breadcrumbs'][] = ['label' => 'Жилые комплексы', 'url' => ['developer/view', 'id' => $model->developer->id]];
$this->params['breadcrumbs'][] = $this->title;

$format = \Yii::$app->formatter;
\yii\web\YiiAsset::register($this);
NewbuildingComplexViewAsset::register($this);
?>
<div class="row flex-row nc-card">
    <div class="col-xs-12 col-md-8">
        <div class="white-block nc-card--info">
            <div class="flex-row">
                <div class="nc-card--info__content">
                    <h2><?= Html::encode($this->title) ?></h2>
                    <p><?= $model->address ?></p>
                    <p class="lg-text">Стоимость объектов:</p>
                    <?= $this->render('_flat_prices', [
                        'model' => $model
                    ]);?>
                </div>
                <div class="nc-card--info__image">
                    <?php if (!is_null($model->logo)) : ?>
                        <?= Html::img([Yii::getAlias("@web/uploads/{$model->logo}")]) ?>
                    <?php else : ?>
                        <?= Html::img([Yii::getAlias("@web/img/newbuilding-complex.png")]) ?>
                    <?php endif ?>
                </div>
            </div>

            <?= Gallery::widget([
                'images' => $model->images,
                'fileField' => 'file',
            ]) ?>

            <?php if (!is_null($model->detail)) : ?>
                <p class="h3 bordered">Описание</p>
                <div class="toggle-desc">
                    <div class="toggle-desc--content">
                        <?= $format->asHtml($model->detail); ?>
                    </div>
                    <button class="toggle-desc--trigger"></button>
                </div>
            <?php endif; ?>

            <div class="nc-card--info__mobile">
                <div class="price-block">
                    <p class="title">
                        Цена
                    </p>
                    <p class="range">
                        <?= $this->render('/common/_price', [
                            'condition' => count($model->flats),
                            'firstPrice' => floor($model->minFlatPrice),
                            'secondPrice' => floor($model->maxFlatPrice),
                            'message' => 'данные отсутсвуют'
                        ]) ?>
                    </p>
                </div>
                <div class="detail-info">
                    <?php if(!is_null($model->minFlatArea) && !is_null($model->maxFlatArea)): ?>
                        <div class="detail-info--item">
                            <span class="name">Площадь</span>
                            <span class="value"><?= $format->asAreaRange($model->minFlatArea, $model->maxFlatArea); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(!is_null($model->materials) && count($model->materials) > 0): ?>
                        <div class="detail-info--item">
                            <span class="name">Материал</span>
                            <span class="value">
                                <?= implode(', ', $model->materials) ?>
                            </span>
                        </div>
                    <?php endif; ?>
                    <div class="detail-info--item">
                        <span class="name">Отделка</span>
                        <span class="value">
                            <?= $this->render('/common/_furnishes', [
                                'furnishes' => $model->furnishes,
                                'noDataMessage' => 'Нет данных'
                            ]) ?>
                        </span>
                    </div>
                    <div class="detail-info--item">
                        <span class="name">Свободно</span>
                        <span class="value"><?= $format->asPercent($model->freeFlats) ?> квартир</span>
                    </div>
                </div>
                <div class="flex-row">
                    <?php if(!is_null($model->minYearlyRate)): ?>
                        <div class="btn btn-red">
                            Ставка от <?= $format->asPercent($model->minYearlyRate) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(!is_null($model->maxDiscount)): ?>
                        <div class="btn btn-red">
                            Cкидка до <?= $format->asPercent($model->maxDiscount) ?>
                        </div>
                    <?php endif; ?>

                    <div class="deadline-block">
                        <span>Ближайшая сдача</span>
                        <span><b><?= !is_null($model->nearestDeadline) ? $format->asQuarterAndYearDate($model->nearestDeadline) : 'нет данных' ?></b></span>
                    </div>
                </div>
               
                <?php if(!is_null($model->advantages) && count($model->advantages) > 0): ?>
                    <?= $this->render('/common/_advantages', [
                        'advantages' => $model->advantages
                    ]);?>
                <?php endif; ?>
            </div>
        </div>

        <?= Placemark::widget([
            'address' => $model->address,
            'longitude' => $model->longitude,
            'latitude' => $model->latitude,
        ]) ?>

        <?php if($newsDataProvider->totalCount > 0): ?>
        <div class="inline-list">
            <p class="h3 bordered">Новости / Акции</p>
            <?= ListView::widget([
                'dataProvider' => $newsDataProvider,
                'itemView' => '/common/_inline-list-item',
                'summary' => '',
                'itemOptions' => [
                    'tag' => false
                ],
                'viewParams' => [
                    'displayType' => true
                ],
                'emptyText' => 'Новостей ещё нет'
            ]); ?>
            <?php if ($newsDataProvider->totalCount > 3) : ?>
                <div class="center">
                    <?= Html::a('Все новости', ['/news', ['newbuilding-complex' => $model->id]]) ?>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?= FlatsChess::widget(['newbuildings' => $model->newbuildings]) ?>
        
        <?php if(!is_null($model->stages) && count($model->stages) > 0): ?>
        <div class="white-block interactions">
            <p class="h3 bordered">Взаимдействие</p>
            <?= $this->render('/common/_stages', [
                'stages' => $model->stages,
                'noDataMessage' => 'Нет данных'
            ]) ?>
        </div>
        <?php endif; ?>

        <?php if(!is_null($model->banks) && count($model->banks) > 0): ?>
            <div class="white-block">
                <p class="h3">
                    Аккредитация банков
                </p>
                <?= $this->render('/common/_accreditation', [
                    'banks' => $model->banks,
                    'newbuildingComplex' => $model,
                    'colSizeClass' => 'col-xs-6 col-sm-4',
                    'noDataMessage' => 'ЖК ещё не аккредитован ни в одном банке',
                ]) ?>
            </div>
        <?php endif; ?>
        
        <?php if(!is_null($model->documents) && count($model->documents) > 0): ?>
            <div class="white-block document-list">
                <p class="h3 bordered">
                    Документация
                </p>
                <div class="document-list--content">
                    <?= $this->render('/common/_documents', [
                        'documents' => $model->documents,
                        'noDataMessage' => 'Нет данных'
                    ])?>
                </div>
                <span class="document-list--trigger"></span>
            </div>
        <?php endif; ?>

        <?php if($newbuildingComplexesDataProvider->totalCount > 0): ?>
            <div class="white-block nc-list">
                <p class="h3 bordered">
                    Другие ЖК этого застройщика
                </p>
                <?= ListView::widget([
                    'dataProvider' => $newbuildingComplexesDataProvider,
                    'itemView' => '/common/_newbuilding-complex-item',
                    'summary' => '',
                    'emptyText' => 'Нет данных',
                    'itemOptions' => [
                        'tag' => false,
                    ],
                    'options' => [
                        'class' => 'delimiter-list flex-row row'
                    ]
                ]); ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="col-md-4">
        <div class="nc-card--sidebar sticky hidden-xs hidden-sm">
            <div class="sidebar white-block scrollbar">
                <div class="price-block">
                    <p class="title">
                        Цена
                    </p>
                    <p class="range">
                        <?= $this->render('/common/_price', [
                            'condition' => count($model->flats),
                            'firstPrice' => floor($model->minFlatPrice),
                            'secondPrice' => floor($model->maxFlatPrice),
                            'message' => 'данные отсутсвуют'
                        ]) ?>
                    </p>
                </div>
                <div class="detail-info">
                    <?php if(!is_null($model->minFlatArea) && !is_null($model->maxFlatArea)): ?>
                        <div class="detail-info--item">
                            <span class="name">Площадь</span>
                            <span class="value"><?= $format->asAreaRange($model->minFlatArea, $model->maxFlatArea); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if(!is_null($model->materials) && count($model->materials) > 0): ?>
                        <div class="detail-info--item">
                            <span class="name">Материал</span>
                            <span class="value">
                                <?= implode(', ', $model->materials) ?>
                            </span>
                        </div>
                    <?php endif; ?>
                    <div class="detail-info--item">
                        <span class="name">Отделка</span>
                        <span class="value">
                            <?= $this->render('/common/_furnishes', [
                                'furnishes' => $model->furnishes,
                                'noDataMessage' => 'Нет данных'
                            ]) ?>
                        </span>
                    </div>
                    <div class="detail-info--item">
                        <span class="name">Свободно</span>
                        <span class="value"><?= $format->asPercent($model->freeFlats) ?> квартир</span>
                    </div>
                </div>
                <div class="deadline-block">
                    <span>Ближайшая сдача</span>
                    <span><b><?= !is_null($model->nearestDeadline) ? $format->asQuarterAndYearDate($model->nearestDeadline) : 'нет данных' ?></b></span>
                </div>
                <?php if(!is_null($model->minYearlyRate)): ?>
                    <div class="btn btn-red">
                        Ставка от <?= $format->asPercent($model->minYearlyRate) ?>
                    </div>
                <?php endif; ?>
                <?php if(!is_null($model->maxDiscount)): ?>
                    <div class="btn btn-red">
                        Cкидка до <?= $format->asPercent($model->maxDiscount) ?>
                    </div>
                <?php endif; ?>
                <?php if(!is_null($model->advantages) && count($model->advantages) > 0): ?>
                    <?= $this->render('/common/_advantages', [
                        'advantages' => $model->advantages
                    ]);?>
                <?php endif; ?>
                <?php if($contactDataProvider->totalCount > 0): ?>
                    <?= ListView::widget([
                        'dataProvider' => $contactDataProvider,
                        'itemView' => '/common/_contact-item',
                        'summary' => '',
                        'itemOptions' => [
                            'tag' => false
                        ],
                        'options' => [
                            'tag' => false,
                        ]
                    ]); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>