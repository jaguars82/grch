<?php
/* @var $model app\models\Newbuilding */
/* @var $this yii\web\View */

use app\assets\NewbuildingViewAsset;
use app\components\widgets\FlatsChess;
use app\components\widgets\FloorLayout;
use app\components\widgets\ImageView;
use app\components\widgets\Placemark;
use app\components\widgets\Gallery;
use app\models\Newbuilding;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\DetailView;
use yii\widgets\ListView;
use yii\widgets\Pjax;

$format = \Yii::$app->formatter;

$this->title = $format->asCapitalize($model->name);
$this->params['breadcrumbs'][] = ['label' => 'Застройщики', 'url' => ['developer/index']];
$this->params['breadcrumbs'][] = ['label' => $model->developer->name, 'url' => ['developer/view', 'id' => $model->developer->id]];
$this->params['breadcrumbs'][] = ['label' => 'Жилые комплексы', 'url' => ['developer/view', 'id' => $model->developer->id]];
$this->params['breadcrumbs'][] = ['label' => $model->newbuildingComplex->name, 'url' => ['newbuilding-complex/view', 'id' => $model->newbuildingComplex->id]];
$this->params['breadcrumbs'][] = ['label' => 'Позиции', 'url' => ['newbuilding-complex/view', 'id' => $model->newbuildingComplex->id]];
$this->params['breadcrumbs'][] = $this->title;


if(!empty($model->address)) {
    $address = $model->address;
} else {
    $address = !is_null($model->newbuildingComplex->address) && !empty($model->newbuildingComplex->address) ? $model->newbuildingComplex->address : NULL;
}

if(!is_null($model->district) || !empty($model->district)) {
    $district = $model->district->name;
} else {
    $district = !is_null($model->newbuildingComplex->district) || !empty($model->newbuildingComplex->district) ? $model->newbuildingComplex->district->name : NULL;
}

\yii\web\YiiAsset::register($this);
NewbuildingViewAsset::register($this);
?>
<div class="row flex-row newbuilding-card">
    <div class="col-xs-12 col-md-8">
        <div class="white-block">
            <h2><?= $format->asCapitalize(Html::encode($model->name)) ?></h2>
            <?php if(!is_null($address)): ?>
            <p class="lg-text bordered">
                <?= $address ?>
            </p>
            <?php endif; ?>
            <div class="newbuilding-card--properties flex-row">
                <div class="left">
                    <div class="newbuilding-card--properties__item">
                        <span>Застройщик</span>
                        <?= Html::a($model->developer->name, ['developer/view', 'id' => $model->developer->id]) ?>
                    </div>
                    <div class="newbuilding-card--properties__item">
                        <span>Позиция</span>
                        <b><?= $format->asCapitalize(Html::encode($model->name)) ?></b>
                    </div>
                    <?php if(!is_null($model->total_floor)): ?>
                        <div class="newbuilding-card--properties__item">
                            <span>Этажей</span>
                            <b><?= $model->total_floor ?></b>
                        </div>
                    <?php endif; ?>
                    <?php if(!is_null($model->deadline)): ?>
                        <div class="newbuilding-card--properties__item">
                            <span>Срок сдачи</span>
                            <b><?= $format->asQuarterAndYearDate($model->deadline) ?></b>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="right">
                    <div class="newbuilding-card--properties__item">
                        <span>Жилой комплекс</span>
                        <?= Html::a($model->newbuildingComplex->name, ['id' => $model->newbuildingComplex->id]) ?>
                    </div>
                    <?php if(!is_null($model->material)): ?>
                        <div class="newbuilding-card--properties__item">
                            <span>Материал</span>
                            <b><?= $format->asCapitalize($model->material) ?></b>
                        </div>
                    <?php endif; ?>
                    <div class="newbuilding-card--properties__item">
                        <span>Отделка</span>
                        <b>
                            <?= $this->render('/common/_furnishes', [
                                'furnishes' => $model->newbuildingComplex->furnishes,
                                'noDataMessage' => 'Нет данных'
                            ]) ?>
                        </b>
                    </div>
                    <div class="newbuilding-card--properties__item">
                        <span>Свободно</span>
                        <b><?= $format->asPercent($model->freeFlats) ?> квартир</b>
                    </div>
                </div>
            </div>
            <?php if(!is_null($model->newbuildingComplex->minYearlyRate) || !is_null($model->maxDiscount)): ?>
            <div class="newbuilding-card--labels flex-row">
                <?php if(!is_null($model->newbuildingComplex->minYearlyRate)): ?>
                    <div class="btn btn-red">
                        Ставка от <?= $format->asPercent($model->newbuildingComplex->minYearlyRate) ?>
                    </div>
                <?php endif; ?>
                
                <?php if(!is_null($model->maxDiscount)): ?>
                    <div class="btn btn-red">
                        Cкидка до <?= $format->asPercent($model->maxDiscount) ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?= $this->render('/common/_floor-layout', [
                'floorLayouts' => $floorLayouts,
            ]);?>

            <?= Gallery::widget([
                'images' => $model->newbuildingComplex->images,
                'fileField' => 'file',
            ]) ?>

            <?php if (!is_null($model->newbuildingComplex->detail)) : ?>
                <p class="h3 bordered">Описание</p>
                <div class="toggle-desc">
                    <div class="toggle-desc--content">
                        <?= $format->asHtml($model->newbuildingComplex->detail); ?>
                    </div>
                    <button class="toggle-desc--trigger"></button>
                </div>
            <?php endif; ?>

            <div class="newbuilding-card--info__mobile">
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
                
                <?php if(!is_null($model->newbuildingComplex->advantages) && count($model->newbuildingComplex->advantages) > 0): ?>
                    <?= $this->render('/common/_advantages', [
                        'advantages' => $model->newbuildingComplex->advantages
                    ]);?>
                <?php endif; ?>
            </div>
        </div>

        <?= Placemark::widget([
            'address' => $model->newbuildingComplex->address,
            'longitude' => $model->newbuildingComplex->longitude,
            'latitude' => $model->newbuildingComplex->latitude,
        ]) ?>

        <?php if($newsDataProvider->totalCount > 0): ?>
        <div class="inline-list">
            <p class="h3 bordered">Новости / Акции</p>
            <?= ListView::widget([
                'dataProvider' => $newsDataProvider,
                'itemView' => '/common/_inline-list-item',
                'summary' => '',
                'itemOptions' => [
                    'tags' => false
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

        <?= FlatsChess::widget(['newbuildings' => $model->newbuildingComplex->newbuildings]) ?>
            
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
                    'banks' => $model->newbuildingComplex->banks,
                    'newbuildingComplex' => $model->newbuildingComplex,
                    'colSizeClass' => 'col-xs-6 col-sm-4',
                    'noDataMessage' => 'Позиция ещё не аккредитована ни в одном банке',
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
    <div class="col-md-4 hidden-xs hidden-sm">
        <div class="sticky">
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
                    
                    <div class="detail-info--item">
                        <span class="name">Площадь</span>
                        <span class="value">43,54 - 108,76 м²</span>
                    </div>
                    
                    <?php if(!is_null($model->material)): ?>
                        <div class="newbuilding-card--properties__item">
                            <span>Материал</span>
                            <b><?= $format->asCapitalize($model->material) ?></b>
                        </div>
                    <?php endif; ?>
                    <?php if(!is_null($model->total_floor)): ?>
                        <div class="detail-info--item">
                            <span class="name">Этажей</span>
                            <span class="value">
                                <?= $model->total_floor ?>
                            </span>
                        </div>
                    <?php endif; ?>
                    <div class="detail-info--item">
                        <span class="name">Отделка</span>
                        <span class="value">
                            <?= $this->render('/common/_furnishes', [
                                'furnishes' => $model->newbuildingComplex->furnishes,
                                'noDataMessage' => 'Нет данных'
                            ]) ?>
                        </span>
                    </div>
                    <div class="detail-info--item">
                        <span class="name">Свободно</span>
                        <span class="value"><?= $format->asPercent($model->freeFlats) ?> квартир</span>
                    </div>
                </div>
                <?php if(!is_null($model->deadline)): ?>
                    <div class="deadline-block">
                        <span>Сдача</span>
                        <span><b><?= $format->asQuarterAndYearDate($model->deadline) ?></b></span>
                    </div>
                <?php endif; ?>

                <?php if(!is_null($model->newbuildingComplex->minYearlyRate)): ?>
                    <div class="btn btn-red">
                        Ставка от <?= $format->asPercent($model->newbuildingComplex->minYearlyRate) ?>
                    </div>
                <?php endif; ?>

                <?php if(!is_null($model->maxDiscount)): ?>
                    <div class="btn btn-red">
                        Cкидка до <?= $format->asPercent($model->maxDiscount) ?>
                    </div>
                <?php endif; ?>

                <?php if(!is_null($model->newbuildingComplex->advantages) && count($model->newbuildingComplex->advantages) > 0): ?>
                    <?= $this->render('/common/_advantages', [
                        'advantages' => $model->newbuildingComplex->advantages
                    ]);?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>