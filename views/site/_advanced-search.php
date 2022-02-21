<?php

use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\DeveloperSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="favorite-search">
    <?php $form = ActiveForm::begin([
        'id' => 'advanced-search',
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <?php /* ?>    
    <div class="col-md-12" style="padding-left: 0">
        <?= $form->field($model, 'address')->textInput([
            'placeholder' => 'Адрес жилого комплекса',
        ])->label(false) ?>
    </div>
    <?php */ ?>

    <div class="col-md-4" style="padding-left: 0">
        <?= $form->field($model, 'region_id')->dropDownList($regions, [
            'id' => 'region-select',
            'prompt' => 'Регион'
        ])->label(false) ?>
    </div>
     
    <div class="col-md-4" style="padding-left: 0">
        <?= $form->field($model, 'city_id')->dropDownList($cities, [
            'id' => 'city-select',
            'prompt' => 'Город'
        ])->label(false) ?>
    </div>

    <div class="col-md-4" style="padding-left: 0">
        <?= $form->field($model, 'district')->dropDownList($districts, [
            'id' => 'district-select',
            'prompt' => 'Район'
        ])->label(false) ?>
    </div>

    <div class="col-md-3" style="padding-left: 0">
        <?= $form->field($model, 'street_name')->textInput([
            'placeholder' => 'Название улицы',
        ])->label(false); ?>
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
    
    <div class="row">
        <div class="col-md-6">
            <div class="col-md-6" style="padding-left: 0">
                <?= $form->field($model, 'is_euro')->checkbox([
                    'style' => 'margin-top: 0'
                ]); ?>
            </div>

            <div class="col-md-6" style="padding-left: 0">
                <?= $form->field($model, 'is_studio')->checkbox([
                    'style' => 'margin-top: 0'
                ]) ?>
            </div>
            
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

        <div class="col-md-6" style="padding-left: 0; padding-right: 30px">
            <div style="margin-top: -5px">
                <?= $form->field($model, 'newbuilding_array[]')->dropDownList($positionArray, ['size' => 5, 'multiple' => true]) ?>
            </div>
            
            <div class="col-md-6" style="padding-left: 0; margin-top: -5px">
                <?= $form->field($model, 'material')->dropDownList($materials, ['prompt' => 'Материал'])->label(false) ?>
            </div>
            
            <div class="col-md-6" style="padding-right: 0; margin-top: -5px">
                <?= $form->field($model, 'newbuilding_status')->dropDownList(\app\models\Newbuilding::$status, ['prompt' => 'Статус позиции'])->label(false) ?>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4" style="padding-left: 15px">
            <?= $form->field($model, 'totalFloor')->textInput([
                'type' => 'number',
                'min' => 1,
                'style' => 'display: inline; width: 80px; margin-left: 10px;'
            ]) ?>
        </div>

        <div class="col-md-3">
            <?= $form->field($model, 'deadline')->widget(DatePicker::class,[
                'dateFormat' => 'dd.MM.yyyy',
                'options' => [
                    'class'=> 'form-control',
                    'style' => 'display: inline; width: 100px; margin-left: 10px;'
                ],
            ]) ?>
        </div>

        <div class="col-md-5">
            <?= $form->field($model, 'update_date')->widget(DatePicker::class,[
                'dateFormat' => 'dd.MM.yyyy',
                'options' => [
                    'class'=> 'form-control',
                    'style' => 'display: inline; width: 100px; margin-left: 10px;'
                ],
            ]) ?>
        </div>
    </div>
    
    <div class="form-group" style="clear: both">
        <?= Html::submitButton('Поиск', ['class' => 'btn btn-success']) ?>        
        <?= Html::a('Очистить', '#', ['class' => 'btn btn-danger advanced-search-clear']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
