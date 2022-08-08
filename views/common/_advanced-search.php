<?php

use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;
use app\models\search\AdvancedFlatSearch;

$format = \Yii::$app->formatter;

$optionalParamsOpened = false;

if (!empty($model->areaFrom)
    or !empty($model->areaTo)
    or !empty($model->floorFrom)
    or !empty($model->floorToTo)
    or !empty($model->totalFloorFrom)
    or !empty($model->totalFloorTo)
    or !empty($model->newbuilding_array)
    or !empty($model->material)
    or !empty($model->newbuilding_status)
    or !empty($model->deadlineyear)) {
        $optionalParamsOpened = true;
    }

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
    
    <!--<?= $form->field($model, 'street_name')->textInput([
        'placeholder' => 'Улица',
    ])->label(false); ?>-->

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
                    //'value' => $model::PRICE_TYPE_ALL
                    'value' => $model->priceType
                ])->label(false) ?>
            </div>
            <div class="col-xs-6">
                <?= $form->field($model, 'priceFrom')->hiddenInput([
                    'placeholder' => 'От',
                    'class' => 'form-control js-price-format'
                ])->label(false); ?>
            </div>
            <div class="col-xs-6">
                <?= $form->field($model, 'priceTo')->hiddenInput([
                    'placeholder' => 'До',
                    'class' => 'form-control js-price-format'
                ])->label(false) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6 text-left">
                <span id="price-from-label">
                    <?php if (!empty($model->priceFrom)): ?>
                    <?= $format->asCurrency($model->priceFrom) ?>
                    <?php else: ?>
                    От
                    <?php endif; ?>
                </span>
            </div>
            <div class="col-xs-6 text-right">
                <span id="price-to-label">
                    <?php if (!empty($model->priceTo)): ?>
                    <?= $format->asCurrency($model->priceTo) ?>
                    <?php else: ?>
                    До
                    <?php endif; ?>
                </span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div id="price-range-for-all-container" class="range-container" <?php /*if ($model->priceType == 1): ?>style="display: none;"<?php endif;*/ ?>>
                    <div id="price-range-for-all">
                        <input />
                        <input />
                    </div>
                    <div id="price-range-for-all-reset" class="range-reset-button"><span class="material-icons-outlined">restart_alt</span></div>
                </div>
                <div id="price-range-for-m2-container" class="range-container" <?php /*if ($model->priceType == 0): ?>style="display: none;"<?php endif;*/ ?>>
                    <div id="price-range-for-m2" >
                        <input />
                        <input />
                    </div>
                    <div id="price-range-for-m2-reset" class="range-reset-button"><span class="material-icons-outlined">restart_alt</span></div>
                </div>
            </div>
        </div>
    </div>

    <?php if (Yii::$app->controller->action->id !== 'map'): ?>
    <div id="optional-params">
    <?php endif; ?>

        <div class="form-group">
            <label class="control-label"> Площадь </label>
            <div class="row">
                <div class="col-xs-6">
                    <?= $form->field($model, 'areaFrom')->hiddenInput([
                        'placeholder' => 'От',
                    ])->label(false) ?>
                </div>
                <div class="col-xs-6">
                    <?= $form->field($model, 'areaTo')->hiddenInput([
                        'placeholder' => 'До',
                    ])->label(false) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6 text-left">
                    <span id="area-from-label">
                        <?php if (!empty($model->areaFrom)): ?>
                        <?= str_replace('.00', '', $format->asArea($model->areaFrom)) ?>
                        <?php else: ?>
                        От
                        <?php endif; ?>
                    </span>
                </div>
                <div class="col-xs-6 text-right">
                    <span id="area-to-label">
                        <?php if (!empty($model->areaTo)): ?>
                        <?= str_replace('.00', '', $format->asArea($model->areaTo)) ?>
                        <?php else: ?>
                        До
                        <?php endif; ?>
                    </span>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div id="area-range-container" class="range-container">
                        <div id="area-range">
                            <input />
                            <input />
                        </div>
                        <div id="area-range-reset" class="range-reset-button"><span class="material-icons-outlined">restart_alt</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label"> Этаж </label>
            <div class="row">
                <div class="col-xs-6">
                    <?= $form->field($model, 'floorFrom')->hiddenInput([
                        'placeholder' => 'От',
                    ])->label(false) ?>
                </div>
                <div class="col-xs-6">
                    <?= $form->field($model, 'floorTo')->hiddenInput([
                        'placeholder' => 'До',
                    ])->label(false) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6 text-left">
                    <span id="floor-from-label">
                        <?php if (!empty($model->floorFrom)): ?>
                        <?= $model->floorFrom ?>
                        <?php else: ?>
                        От
                        <?php endif; ?>
                    </span>
                </div>
                <div class="col-xs-6 text-right">
                    <span id="floor-to-label">
                        <?php if (!empty($model->floorTo)): ?>
                        <?= $model->floorTo ?>
                        <?php else: ?>
                        До
                        <?php endif; ?>
                    </span>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div id="floor-range-container" class="range-container">
                        <div id="floor-range">
                            <input />
                            <input />
                        </div>
                        <div id="floor-range-reset" class="range-reset-button"><span class="material-icons-outlined">restart_alt</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label"> Этажность </label>
            <div class="row">
                <div class="col-xs-6">
                    <?= $form->field($model, 'totalFloorFrom')->hiddenInput([
                        'placeholder' => 'От',
                    ])->label(false); ?>
                </div>
                <div class="col-xs-6">
                    <?= $form->field($model, 'totalFloorTo')->hiddenInput([
                        'placeholder' => 'От',
                    ])->label(false); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6 text-left">
                    <span id="total-floor-from-label">
                        <?php if (!empty($model->totalFloorFrom)): ?>
                        <?= $model->totalFloorFrom ?>
                        <?php else: ?>
                        От
                        <?php endif; ?>
                    </span>
                </div>
                <div class="col-xs-6 text-right">
                    <span id="total-floor-to-label">
                        <?php if (!empty($model->totalFloorTo)): ?>
                        <?= $model->totalFloorTo ?>
                        <?php else: ?>
                        До
                        <?php endif; ?>
                    </span>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div id="total-floor-range-container" class="range-container">
                        <div id="total-floor-range">
                            <input />
                            <input />
                        </div>
                        <div id="total-floor-range-reset" class="range-reset-button"><span class="material-icons-outlined">restart_alt</span></div>
                    </div>
                </div>
            </div>
        </div>

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

    <?php if (Yii::$app->controller->action->id !== 'map'): ?>
    </div>
    <?php endif; ?>

    <?php if (Yii::$app->controller->action->id !== 'map'): ?>
    <div>
        <a id="more-less-params" class="<?php if ($optionalParamsOpened === false): ?>closed<?php endif; ?>" href="javascript:void(0);">
            <span class="more-less-label">
                <?php if($optionalParamsOpened === false): ?>
                Больше параметров
                <?php else: ?>
                Меньше параметров
                <?php endif; ?>
            </span>
            <span class="material-icons-outlined more-less-icon">
                <?php if($optionalParamsOpened): ?>
                expand_less
                <?php else: ?>
                chevron_right
                <?php endif; ?>
            </span>
        </a>
    </div>
    <?php endif; ?>

    <div class="text-center">
        <?= Html::submitButton('Найти', ['class' => 'btn btn-primary']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>