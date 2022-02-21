<?php
/* @var $this yii\web\View */
/* @var $model app\models\search\DeveloperSearch */
/* @var $form yii\widgets\ActiveForm */

use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;
?>

<div class="map-search">
    <?php $form = ActiveForm::begin([
        'id' => 'map-search',
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    
    <div style="display: none">
        <?= $form->field($model, 'selected_points[]')->hiddenInput(['class' => 'input-point-template'])->label(false)?>
    </div>

    <div class="col-md-12" style="margin-top: 15px;">
        <?= $form->field($model, 'region_id')->dropDownList($regions, [
            'id' => 'region-select',
            'prompt' => 'Регион'
        ])->label(false) ?>
    </div>
     
    <div class="col-md-6">
        <?= $form->field($model, 'city_id')->dropDownList($cities, [
            'id' => 'city-select',
            'prompt' => 'Город'
        ])->label(false) ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($model, 'district')->dropDownList($districts, [
            'id' => 'district-select',
            'prompt' => 'Район',
            'multiple' => true
        ])->label(false) ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($model, 'street_name')->textInput([
            'placeholder' => 'Название улицы',
        ])->label(false); ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($model, 'developer')->dropDownList($developers, ['prompt' => 'Застройщик'])->label(false) ?>
    </div>
    
    <div class="col-md-6">
        <?= $form->field($model, 'newbuilding_complex')->dropDownList($newbuildingComplexes, ['prompt' => 'Жилой комплекс'])->label(false) ?>
    </div>
    
    <div class="col-md-6">
        <?= $form->field($model, 'rooms')->dropDownList([1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => '5+'], ['prompt' => 'Количество комнат'])->label(false) ?>
    </div>
    
    <div class="col-md-12">
        <?= $form->field($model, 'newbuilding_array[]')->dropDownList($positionArray, ['size' => 3, 'multiple' => true]) ?>
    </div>
    
    <div class="col-md-12">
        <div class="col-md-8" style="padding-left: 0">
            <?= $form->field($model, 'priceFrom')->textInput([
                'placeholder' => 'От',
                'style' => 'display: inline; width: 110px; margin-left: 20px;'
            ]) ?>
        </div>

        <div class="col-md-4" style="padding-left: 0">
            <?= $form->field($model, 'priceTo')->textInput([
                'placeholder' => 'До',
                'style' => 'display: inline; width: 110px;'
            ])->label(false) ?>
        </div>
    </div>
    
    <div class="col-md-12">
        <div class="col-md-8" style="padding-left: 0">
            <?= $form->field($model, 'areaFrom')->textInput([
                'placeholder' => 'От',
                'style' => 'display: inline; width: 110px; margin-left: 30px;'
            ]) ?>
        </div>

        <div class="col-md-4" style="padding-left: 0">
            <?= $form->field($model, 'areaTo')->textInput([
                'placeholder' => 'До',
                'style' => 'display: inline; width: 110px;'
            ])->label(false) ?>
        </div>
    </div>
    
    <div class="col-md-12">
        <div class="col-md-8" style="padding-left: 0">
            <?= $form->field($model, 'floorFrom')->textInput([
                'placeholder' => 'От',
                'style' => 'display: inline; width: 110px; margin-left: 60px;'
            ]) ?>
        </div>

        <div class="col-md-4" style="padding-left: 0">
            <?= $form->field($model, 'floorTo')->textInput([
                'placeholder' => 'До',
                'style' => 'display: inline; width: 110px;'
            ])->label(false) ?>
        </div>
    </div>
    
    <div class="col-md-6">
        <?= $form->field($model, 'is_euro')->checkbox([
            'style' => 'margin-top: 0'
        ]); ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($model, 'is_studio')->checkbox([
            'style' => 'margin-top: 0'
        ]) ?>
    </div>

    <div class="col-md-12">
        <?= $form->field($model, 'totalFloor')->textInput([
            'type' => 'number',
            'min' => 1,
        ]) ?>
    </div>
    
    <div class="col-md-12">
        <?= $form->field($model, 'deadline')->widget(DatePicker::class,[
            'dateFormat' => 'dd.MM.yyyy',
            'options' => [
                'class'=> 'form-control',
            ],
        ]) ?>
    </div>
    
    <div class="col-md-12">
        <?= $form->field($model, 'update_date')->widget(DatePicker::class,[
            'dateFormat' => 'dd.MM.yyyy',
            'options' => [
                'class'=> 'form-control',
            ],
        ]) ?>
    </div>
    
    <div class="col-md-12">
        <div class="col-md-6" style="padding-left: 0">
            <?= $form->field($model, 'material')->dropDownList($materials, ['prompt' => 'Материал'])->label(false) ?>
        </div>

        <div class="col-md-6" style="padding-right: 0">
            <?= $form->field($model, 'newbuilding_status')->dropDownList(\app\models\Newbuilding::$status, ['prompt' => 'Статус позиции'])->label(false) ?>
        </div>
    </div>
    
    <?php ActiveForm::end(); ?>
</div>