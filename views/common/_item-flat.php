<?php
/* @var $model app\models\Flat */

use app\components\widgets\FloorLayout;
use yii\helpers\Html;
use yii\helpers\Url;

$format = \Yii::$app->formatter;
?>

<div class="col-md-12" style="padding-left: 0; padding-right: 0">
    <div class="panel panel-default">
        <?php if($model->isFavorite()): ?>
            <span class="glyphicon glyphicon-star" style="position: absolute; left: 5px; top: 3px; color: red" title="Квартира находиться в избранном"></span>
        <?php endif ?>

        <div class="panel-body" style="padding: 10px; padding-left: 14px">
            <div class="row">
                <div class="col-md-2 text-center">
                    <?php if(!is_null($model->layout)): ?>
                        <?= Html::img(["/uploads/{$model->layout}"], ['class' => 'photo', 'style' => 'max-width: 100%; height: 60px']) ?>
                    <?php else: ?>
                        <?= Html::img([Yii::getAlias("@web/img/flat.png")], ['height' => 60]) ?>
                    <?php endif ?>
                </div>
                <div class="col-md-10">
                    <div class="row">
                        <div class="col-md-6">
                            <!--span class="glyphicon glyphicon-star" style="margin-right: 3px"></span-->
                            <?= Html::a($model->roomsTitle, ['flat/view', 'id' => $model->id]) ?>
                        </div>

                        <div class="col-md-6 text-right">
                            <?= $this->render('/common/_price', [
                                'condition' => $model->hasDiscount(),
                                'firstPrice' => floor($model->cashPriceWithDiscount),
                                'secondPrice' => $model->price_cash,
                                'onePrice' => $model->price_cash
                            ]) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-9">
                            <?= Html::a($model->newbuildingComplex->name, ['newbuilding-complex/view', 'id' => $model->newbuildingComplex->id]) ?>
                            <?= Html::a($model->newbuildingComplex->developer->name, ['developer/view', 'id' => $model->newbuildingComplex->developer->id]) ?>
                        </div>

                        <div class="col-md-3 text-right">
                            <?= $this->render('/common/_price', [
                                'condition' => $model->hasDiscount(),
                                'firstPrice' => floor($model->cashPriceWithDiscount),
                                'secondPrice' => $model->price_cash,
                                'onePrice' => $model->price_cash
                            ]) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            Адрес дома: <?= is_null($model->newbuildingComplex->address) ? 'Нет данных' : $model->newbuildingComplex->address ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-9">
                            <?= $format->asArea($model->area) ?>,

                            Этаж:
                            <?php if(!is_null($floorLayout = $model->floorLayout)): ?>
                                <?= Html::a($format->asFloor($model->floor, $model->newbuilding->total_floor), "/uploads/{$floorLayout->image}", ['class' => 'floor-layout', 'data-viewer-id' => $model->id]) ?>
                            <?php else: ?>
                                <?= $format->asFloor($model->floor, $model->newbuilding->total_floor) ?>
                            <?php endif ?>,

                            Сдача: <?= is_null($model->newbuilding->deadline) ? 'Нет данных' : $format->asQuarterAndYearDate($model->newbuilding->deadline, false) ?>,
                            <?php if(!is_null($model->newbuilding->newbuildingComplex->district)): ?>
                                <?= $format->asDistrict($model->newbuilding->newbuildingComplex->district->name) ?>
                            <?php else: ?>
                                Нет данных о р-не
                            <?php endif ?>
                        </div>

                        <?php if(!is_null($model->updated_at) && ($model->updated_at !== '0000-00-00 00:00:00')): ?>
                            <div class="col-md-3 text-right">
                                <?= $format->asDate($model->updated_at, 'php:d.m.Y H:i') ?>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= FloorLayout::widget(['flat' => $model]) ?>