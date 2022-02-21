<?php

use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\DeveloperSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="action-flat-search-form" style="display: none;">
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
                'id' => 'newbuilding-complex-select2',
                'data-placeholder' => 'Жилой комплекс',
            ])->label(false) ?>
        </div>
    </div>

    <h3>Информация о квартирах</h3>


    <div class="row">
        <div class="col-md-12" >
            <?= $form->field($model, 'priceFrom')->textInput([
                'placeholder' => 'От',
                'style' => 'display: inline;'

            ]) ?>
        </div>

        <div class="col-md-12" >
            <?= $form->field($model, 'priceTo')->textInput([
                'placeholder' => 'До',
                'style' => 'display: inline;'

            ])->label(false) ?>
        </div>


        <div class="col-md-12" >
            <?= $form->field($model, 'areaFrom')->textInput([
                'placeholder' => 'От',
            ]) ?>
        </div>

        <div class="col-md-12" >
            <?= $form->field($model, 'areaTo')->textInput([
                'placeholder' => 'До',
            ])->label(false) ?>
        </div>

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

        <div class="col-md-12" >
            <?= $form->field($model, 'rooms', [
                'options' => [
                    'class' => 'form-group inline-select'
                ]
            ])->dropDownList([1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => '5+'], [
                'prompt' => '',
                'data-placeholder' => 'Количество комнат'
            ])->label(false) ?>
        </div>



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
        
        <div class="col-md-12" >
            <?= $form->field($model, 'deadline')->widget(DatePicker::class,[
                'dateFormat' => 'dd.MM.yyyy',
                'options' => [
                    'class'=> 'form-control',
                ],
            ]) ?>
        </div>

        <div class="col-md-12" >
            <?= $form->field($model, 'update_date')->widget(DatePicker::class,[
                'dateFormat' => 'dd.MM.yyyy',
                'options' => [
                    'class'=> 'form-control',
                ],
            ]) ?>
        </div>
    </div>

    <div class="form-group" style="clear: both">
        <?= Html::button('Поиск', ['class' => 'btn btn-success js-search-flats']) ?>
        <?= Html::a('Очистить', '#', ['class' => 'btn btn-danger advanced-search-clear']) ?>
    </div>
</div>
<div class="action-flat-search-result col-xs-12"></div>