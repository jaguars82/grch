<?php
/* @var $model app\models\Flat */

use app\components\widgets\FloorLayout;
use yii\helpers\Html;
use yii\helpers\Url;

$format = \Yii::$app->formatter;
?>

    <div class="col-md-12" style="padding-left: 0; padding-right: 0">
        <div class="panel panel-default">
            <div class="panel-body" style="padding: 10px; padding-left: 14px">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <?= Html::a($model->roomsTitle, ['flat/view', 'id' => $model->id], ['target' => '_blank']) ?>, <?= $format->asArea($model->area) ?>, <?= $format->asFloor($model->floor, $model->newbuilding->total_floor) ?> эт., Сдача: <?= (is_null($model->newbuilding->deadline) ? 'Нет данных' : strtotime(date("Y-m-d")) > strtotime($model->newbuilding->deadline)) ? 'позиция сдана' : $format->asQuarterAndYearDate($model->newbuilding->deadline, false) ?>, <?= Html::a($model->newbuildingComplex->name, ['newbuilding-complex/view', 'id' => $model->newbuildingComplex->id], ['target' => '_blank']) ?> (<?= Html::a($model->newbuildingComplex->developer->name, ['developer/view', 'id' => $model->newbuildingComplex->developer->id], ['target' => '_blank']) ?>)

                            </div>

                            <div class="col-md-12">
                                <?= $this->render('/common/_price', [
                                    'condition' => $model->hasDiscount(),
                                    'firstPrice' => floor($model->cashPriceWithDiscount),
                                    'secondPrice' => $model->price_cash,
                                    'onePrice' => $model->price_cash
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?= FloorLayout::widget(['flat' => $model]) ?>