<?php

use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\NewbuildingComplexSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="newbuilding-complex-search">
    <?php $form = ActiveForm::begin([
        'id' => 'form-offer-index',
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data' => [
                'form-name' => 'OfferSearch',
            ],
        ],
    ]); ?>

    <div class="row">        
        <div class="col-md-7" style="margin-right: 50px">
            <?= $form->field($model, 'address')->textInput([
                'style' => 'width: 690px',
                'placeholder' => 'Адрес квартиры',
            ])->label(false) ?>
        </div>

        <div class="col-md-3" style="padding-left: 0">
            <?= $form->field($model, 'dateFrom')->widget(DatePicker::class,[
                'dateFormat' => 'dd.MM.yyyy',
                'options' => ['class'=> 'form-control', 'style' => 'display: inline; width: 100px; margin-left: 10px;', 'placeholder' => 'От'],
            ]) ?>
        </div>

        <div class="col-md-2" style="padding-left: 0; margin-left: -50px;">
            <?= $form->field($model, 'dateTo', [
                'template' => '
                    {label}{input}
                    <span style="float:right">
                    <a href="#" class="offer-index-search-btn">
                      <i style="margin-right: 5px" class="glyphicon glyphicon-search"></i>
                    </a>
                    <a href="#" class="offer-index-search-clear">
                      <i style="margin-right: 5px" class="glyphicon glyphicon-remove"></i>
                    </a>
                    </span>
                    {hint}{error}'
            ])->widget(DatePicker::class,[
                'dateFormat' => 'dd.MM.yyyy',
                'options' => ['class'=> 'form-control', 'style' => 'display: inline; width: 100px;', 'placeholder' => 'До'],
            ])->label(false) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
