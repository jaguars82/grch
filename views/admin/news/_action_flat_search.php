<?php

use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\DeveloperSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="action-flat-search-form" style="display: none; border: solid thin #ccc; border-radius: 5px; padding: 10px 15px; background: #eee; margin-bottom: 20px;">
    <h3>Информация о ЖК</h3>

    <div class="row">
        <div class="col-md-6" >
            <?= $form->field($model, 'developer', [
                'options' => [
                    'class' => 'form-group inline-select'
                ]
            ])->dropDownList($developers, [
                'prompt' => '',
                'data-placeholder' => 'Застройщик',
                'id' => 'developer-select'
            ])->label(false) ?>
        </div>

        <div class="col-md-6" >
            <?= $form->field($model, 'newbuilding_complex', [
                'options' => [
                    'class' => 'form-group inline-select'
                ]
            ])->dropDownList($newbuildingComplexes, [
                'multiple' => true,
                'id' => 'newbuilding-complex-select2',
                'data-placeholder' => 'Жилой комплекс',
            ])->label(false) ?>
        </div>

        <!-- newbuilding -->
        <div class="col-md-6" >
            <?= $form->field($model, 'newbuilding', [
                'options' => [
                    'class' => 'form-group inline-select'
                ]
            ])->dropDownList($newbuildings, [
                'multiple' => true,
                'id' => 'newbuildings-select2',
                'data-placeholder' => 'Позиция',
            ])->label(false) ?>
        </div>

        <!-- entrance -->
        <div class="col-md-6" >
            <?= $form->field($model, 'entrance', [
                'options' => [
                    'class' => 'form-group inline-select'
                ]
            ])->dropDownList($entrances, [
                'multiple' => true,
                'id' => 'entrance-select2',
                'data-placeholder' => 'Подъезд',
            ])->label(false) ?>
        </div>

    </div>

    <h3>Информация о квартирах</h3>

    <div class="row">
        <div class="col-md-12">
            <h4 style="font-weight: 400;">Стоимость</h4>
        </div>
        <div class="col-md-6" >
            <?= $form->field($model, 'priceFrom')->textInput([
                'placeholder' => 'От',
                'style' => 'display: inline;'

            ])->label(false) ?>
        </div>

        <div class="col-md-6" >
            <?= $form->field($model, 'priceTo')->textInput([
                'placeholder' => 'До',
                'style' => 'display: inline;'

            ])->label(false) ?>
        </div>


        <div class="col-md-12">
            <h4 style="font-weight: 400;">Площадь</h4>
        </div>
        <div class="col-md-6" >
            <?= $form->field($model, 'areaFrom')->textInput([
                'placeholder' => 'От',
            ])->label(false) ?>
        </div>

        <div class="col-md-6" >
            <?= $form->field($model, 'areaTo')->textInput([
                'placeholder' => 'До',
            ])->label(false) ?>
        </div>
        
        <!-- set of particular floors -->
        <div class="col-md-6">
            <?= $form->field($model, 'floorsSet')
                ->dropDownList([1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5], [
                    'multiple' => true,
                    'class' => 'form-control',
                    'size' => 6,
                ]) ?>
        </div>


        <div class="col-md-6" >
            <?= $form->field($model, 'rooms')
                ->dropDownList([1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => '5+'], [
                    'multiple' => true,
                    'class' => 'form-control',
                    'size' => 6,
                ]) ?>
        </div>


        <!--
        <div class="col-md-12" >
            <?= $form->field($model, 'floorFrom')->textInput([
                'placeholder' => 'От',
            ]) ?>
        </div>

        <div class="col-md-12" >
            <?= $form->field($model, 'floorTo')->textInput([
                'placeholder' => 'До',
            ])->label(false) ?>
        </div>
        -->


        <div class="col-md-12">
            <?= $form->field($model, 'material', [
                'options' => [
                    'class' => 'form-group inline-select'
                ]
            ])->dropDownList($materials, [
                'prompt' => '',
                'data-placeholder' => 'Материал'
            ])->label(false) ?>
        </div>
        
        <div class="col-md-6" >
            <?= $form->field($model, 'deadline')->widget(DatePicker::class,[
                'dateFormat' => 'dd.MM.yyyy',
                'options' => [
                    'class'=> 'form-control',
                ],
            ]) ?>
        </div>

        <div class="col-md-6" >
            <?= $form->field($model, 'update_date')->widget(DatePicker::class,[
                'dateFormat' => 'dd.MM.yyyy',
                'options' => [
                    'class'=> 'form-control',
                ],
            ]) ?>
        </div>

    </div>

    
    
    <div class="form-group" style="clear: both">
        <?= Html::button('Поиск по заданным параметрам', ['class' => 'btn btn-success js-search-flats']) ?>
        <!--<?= Html::a('Очистить', '#', ['class' => 'btn btn-danger advanced-search-clear']) ?>-->
    </div>

    <div class="action-flat-search-result" style="max-height: 500px; overflow: auto; padding: 0 5px;"></div>

</div>


