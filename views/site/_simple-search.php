<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\SimpleFlatSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="main-search">
    <?php $form = ActiveForm::begin([
        'id' => 'main-search',
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    
    <!--div class="col-md-6" style="padding-left: 0"-->
        <!--?= $form->field($model, 'address')->textInput([
            'placeholder' => 'Адрес жилого комплекса',
        ])->label(false) ?-->
    <!--/div-->

    <div class="col-md-3" style="padding-left: 0">
        <?= $form->field($model, 'district_id')->dropDownList($districts, ['prompt' => 'Район'])->label(false) ?>
    </div>
    
    <div class="col-md-3" style="padding-left: 0">
        <?= $form->field($model, 'developer')->dropDownList($developers, ['prompt' => 'Застройщик'])->label(false) ?>
    </div>
    
    <div class="col-md-3" style="padding-left: 0">
        <?= $form->field($model, 'newbuilding_complex')->dropDownList($newbuildingComplexes, ['prompt' => 'Жилой комплекс'])->label(false) ?>
    </div>
    
    <div class="col-md-3" style="padding-left: 0">
        <?= $form->field($model, 'rooms')->dropDownList([1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => '5+'], ['prompt' => 'Количество комнат'])->label(false) ?>
    </div>
    
    <div class="col-md-4" style="padding-left: 0">
        <?= $form->field($model, 'priceFrom')->textInput([
            'placeholder' => 'От',
            'style' => 'display: inline; width: 100px; margin-left: 10px;'
        ]) ?>
    </div>
        
    <div class="col-md-2" style="padding-left: 0; margin-left: -50px; margin-right: 50px">
        <?= $form->field($model, 'priceTo')->textInput([
            'placeholder' => 'До',
            'style' => 'display: inline; width: 100px;'
        ])->label(false) ?>
    </div>
    
    <div class="col-md-6" style="padding-left: 0; margin-left: -50px; margin-top: 10px">
        <div id="slider-range"></div>
    </div>

    <!--div class="col-md-4" style="padding-left: 0"-->
        <!--?= $form->field($model, 'areaFrom')->textInput([
            'placeholder' => 'От',
            'style' => 'display: inline; width: 125px; margin-left: 15px;'
        ]) ?-->
    <!--/div-->

    <!--div class="col-md-2" style="padding-left: 0; margin-left: -20px; margin-right: 20px"-->
        <!--?= $form->field($model, 'areaTo')->textInput([
            'placeholder' => 'До',
            'style' => 'display: inline; width: 125px;'
        ])->label(false) ?-->
    <!--/div-->
    
    <div class="form-group" style="clear: both">
        <?= Html::submitButton('Поиск', ['class' => 'btn btn-success']) ?>        
        <?= Html::a('Очистить', '#', ['class' => 'btn btn-danger main-search-clear']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
