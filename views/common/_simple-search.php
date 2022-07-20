<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\search\AdvancedFlatSearch;

/* @var $this yii\web\View */
/* @var $model app\models\search\SimpleFlatSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="container">
    <?php $form = ActiveForm::begin([
        'id' => 'main-search',
        'action' => ['site/search'],
        'method' => 'get',
    ]); ?>
    <div class="flex-row search-index--form">
        <?= $form->field($model, 'district', [
            'options' => [
                'class' => 'search-index--form__item main-select district'
            ]
        ])->dropDownList($districts, [
            'prompt' => '',
            'data-placeholder' => 'Район', 
            'data-close-on-select' => 'false',
            'multiple' => true
        ])->label(false) ?>
        
        <div class="search-index--form__item rooms-count">
            <div class="rooms-count-trigger">
                Количество комнат
            </div>
            <div class="rooms-count-dropdown">
                <?= $form->field($model, 'roomsCount')->checkboxList([1 => 1, 2 => 2, 3 => 3, 4 => '4+'], [
                    'prompt' => '',
                    'class' => 'radio-row rooms-count-select',
                    'item' => function ($index, $label, $name, $checked, $value) {
                        return '<label>' . Html::checkbox($name, $checked, ['value' => $value]) . '<span>' . $label . '</span></label>';
                    }
                ])->label(false) ?>

                <?= $form->field($model, 'flatType')->radioList($model::$flat_types, [
                    'prompt' => '',
                    'class' => 'checkbox-group rooms-layout-select',
                    'item' => function ($index, $label, $name, $checked, $value) {
                        return '<div class="form-group"><label>' . Html::radio($name, $checked, ['value' => $value]) . '<span>' . $label . '</span></label></div>';
                    },
                    'value' => AdvancedFlatSearch::FLAT_TYPE_STANDARD
                ])->label(false) ?>
            </div>
        </div>

        <?= $form->field($model, 'developer', [
            'options' => [
                'class' => 'search-index--form__item main-select developer'
            ]
        ])->dropDownList($developers, [
            'prompt' => '',
            'data-placeholder' => 'Застройщик',
            'data-close-on-select' => 'false',
            'multiple' => true,
        ])->label(false) ?>

        <?= $form->field($model, 'newbuilding_complex', [
            'options' => [
                'class' => 'search-index--form__item main-select newbuilding-complex'
            ]
        ])->dropDownList($newbuildingComplexes, [
            'prompt' => '',
            'data-placeholder' => 'Жилой комплекс', 
            'data-close-on-select' => 'false',
            'multiple' => true
        ])->label(false) ?>

        <div class="search-index--form__item price-select">
            <div class="price-select-trigger">
                Цена
            </div>
            <div class="price-select-dropdown">
                <?= $form->field($model, 'priceType')->radioList($model::$price_types, [
                    'prompt' => '',
                    'class' => 'checkbox-group',
                    'item' => function ($index, $label, $name, $checked, $value) {
                        return '<div class="form-group"><label>' . 
                                    Html::radio($name, $checked, ['value' => $value]) . 
                                    '<span>' . $label . '</span></label></div>';
                    },
                    'value' => $model::PRICE_TYPE_ALL
                ])->label(false) ?>

                <div class="flex-row">
                    <?= $form->field($model, 'priceFrom')->textInput([
                        'placeholder' => 'От',
                        'class' => 'form-control js-price-format'
                    ])->label(false) ?>
                    <?= $form->field($model, 'priceTo')->textInput([
                        'placeholder' => 'До',
                        'class' => 'form-control js-price-format'
                    ])->label(false) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="flex-row search-index--buttons">
        <button class="btn btn-white js-map-search">Показать на карте</button>
        <?= Html::submitButton('Найти', ['class' => 'btn btn-primary']) ?>     
    </div>
    
    <?php ActiveForm::end(); ?>
</div>
