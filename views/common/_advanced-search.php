<?php

use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;
use app\models\search\AdvancedFlatSearch;

/* @var $this yii\web\View */
/* @var $model app\models\search\DeveloperSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin([
    'id' => $id,
    'action' => ['index'],
    'method' => 'get',
]); ?>
<div class="search-filter scrollbar">
    <?= $form->field($model, 'region_id', [
        'options' => [
            'class' => 'form-group inline-select'
        ]
    ])->dropDownList($regions, [
        'id' => 'region-select',
        'prompt' => '',
        'data-placeholder' => 'Регион', 
    ])->label(false) ?>
    
    <?= $form->field($model, 'city_id', [
        'options' => [
            'class' => 'form-group inline-select'
        ]
    ])->dropDownList($cities, [
        'id' => 'city-select',
        'prompt' => '',
        'data-placeholder' => 'Город',
    ])->label(false) ?>
    
    <?= $form->field($model, 'district', [
        'options' => [
            'class' => 'form-group inline-select'
        ]
    ])->dropDownList($districts, [
        'id' => 'district-select',
        'prompt' => '',
        'data-placeholder' => 'Район',
        'data-close-on-select' => 'false',
        'multiple' => true
    ])->label(false) ?>
    
    <?= $form->field($model, 'street_name')->textInput([
        'placeholder' => 'Улица',
    ])->label(false); ?>

    <?= $form->field($model, 'developer', [
        'options' => [
            'class' => 'form-group inline-select'
        ]
    ])->dropDownList($developers, [
        'prompt' => '',
        'data-placeholder' => 'Застройщик',
        'data-close-on-select' => 'false',
        'multiple' => true
    ])->label(false) ?>

    
    <?= $form->field($model, 'newbuilding_complex', [
        'options' => [
            'class' => 'form-group inline-select'
        ]
    ])->dropDownList($newbuildingComplexes, [
        'prompt' => '',
        'data-placeholder' => 'Жилой комплекс',
        'data-close-on-select' => 'false',
        'multiple' => true
    ])->label(false) ?>

    <?= $form->field($model, 'newbuilding_array[]', [
        'options' => [
            'class' => 'form-group inline-select'
        ]
    ])->dropDownList($positionArray, [
        'prompt' => '',
        'data-placeholder' => 'Строительная позиция',
        'data-close-on-select' => 'false',
        'multiple' => true
    ]) ?>

    <div class="form-group">
        <label class="control-label"> Количество комнат </label>
        
        <?= $form->field($model, 'roomsCount')->checkboxList([1 => 1, 2 => 2, 3 => 3, 4 => '4+'], [
            'prompt' => '',
            'class' => 'radio-row rooms-count-select',
            'item' => function ($index, $label, $name, $checked, $value) {
                return '<label>' . Html::checkbox($name, $checked, ['value' => $value]) . '<span>' . $label . '</span></label>';
            }
        ])->label(false) ?>
    </div>

    <?= $form->field($model, 'flatType')->radioList($model::$flat_types, [
        'prompt' => '',
        'class' => 'rooms-layout-select checkbox-group',
        'item' => function ($index, $label, $name, $checked, $value) {
            return '<div class="form-group"><label>' . Html::radio($name, $checked, ['value' => $value]) . '<span>' . $label . '</span></label></div>';
        },
        'value' => AdvancedFlatSearch::FLAT_TYPE_STANDARD
    ])->label(false) ?>

    <div class="form-group">
        <label class="control-label"> Стоимость </label>
        <div class="row">
            <div class="col-xs-12">
                <?= $form->field($model, 'priceType')->radioList($model::$price_types, [
                    'prompt' => '',
                    'class' => 'checkbox-group price-type',
                    'item' => function ($index, $label, $name, $checked, $value) {
                        return '<div class="form-group"><label>' . 
                                    Html::radio($name, $checked, ['value' => $value]) . 
                                    '<span>' . $label . '</span></label></div>';
                    },
                    'value' => $model::PRICE_TYPE_ALL
                ])->label(false) ?>
            </div>
            <div class="col-xs-6">
                <?= $form->field($model, 'priceFrom')->textInput([
                    'placeholder' => 'От',
                    'class' => 'form-control js-price-format'
                ])->label(false); ?>
            </div>
            <div class="col-xs-6">
                <?= $form->field($model, 'priceTo')->textInput([
                    'placeholder' => 'До',
                    'class' => 'form-control js-price-format'
                ])->label(false) ?>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label"> Площадь </label>
        <div class="row">
            <div class="col-xs-6">
                <?= $form->field($model, 'areaFrom')->textInput([
                    'placeholder' => 'От',
                ])->label(false) ?>
            </div>
            <div class="col-xs-6">
                <?= $form->field($model, 'areaTo')->textInput([
                    'placeholder' => 'До',
                ])->label(false) ?>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label"> Этаж </label>
        <div class="row">
            <div class="col-xs-6">
                <?= $form->field($model, 'floorFrom')->textInput([
                    'placeholder' => 'От',
                ])->label(false) ?>
            </div>
            <div class="col-xs-6">
                <?= $form->field($model, 'floorTo')->textInput([
                    'placeholder' => 'До',
                ])->label(false) ?>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label"> Этажность </label>
        <div class="row">
            <div class="col-xs-6">
                <?= $form->field($model, 'totalFloorFrom')->textInput([
                    'placeholder' => 'От',
                ])->label(false); ?>
            </div>
            <div class="col-xs-6">
                <?= $form->field($model, 'totalFloorTo')->textInput([
                    'placeholder' => 'От',
                ])->label(false); ?>
            </div>
        </div>
    </div>

    <?= $form->field($model, 'material', [
        'options' => [
            'class' => 'form-group inline-select'
        ]
    ])->dropDownList($materials, [
        'prompt' => '',
        'data-placeholder' => 'Материал',
    ])->label(false) ?>

    <?= $form->field($model, 'newbuilding_status', [
        'template' => "<label>{input}<span>Дом сдан</span></label>{error}",
    ])->checkbox([
        'value' => \app\models\Newbuilding::STATUS_FINISH,
    ], false)->label(false) ?>

    <?= $form->field($model, 'deadlineYear', [
        'options' => [
            'class' => 'form-group inline-select'
        ]
    ])->dropDownList($deadlineYears, [
        'prompt' => '',
        'data-placeholder' => 'Год сдачи',
    ])->label(false) ?>

    <div class="text-center">
        <?= Html::submitButton('Найти', ['class' => 'btn btn-primary']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>