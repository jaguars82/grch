<?php
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\models\Newbuilding */
/* @var $this yii\web\View */

use app\components\widgets\InputSelect;
use app\assets\NewbuildingFormAsset;
use app\models\Newbuilding;
use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

NewbuildingFormAsset::register($this);
?>

<div class="newbuilding-form">
    <?php $form = ActiveForm::begin(['id' => 'developer-form']); ?>
    
    <div class="display: none"><?= $form->field($model, 'newbuilding_complex_id')->hiddenInput()->label(false) ?></div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'azimuth')->textInput() ?>
            <?= $form->field($model, 'total_floor')->textInput() ?>
            <?= $form->field($model, 'material')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'region_id', [
                'options' => [
                    'class' => 'form-group inline-select'
                ]
            ])->dropDownList($regions, [
                'id' => 'region-select',
                'prompt' => '',
                'data-placeholder' => 'Регион'
            ])->label('Регион') ?>

            <div class="row" style="margin-bottom: 0">
                <div class="col-md-6">
                    <?= InputSelect::widget([
                        'array' => $cities, 
                        'checkedArray' => [$model->city_id], 
                        'field' => 'NewbuildingForm[city_id]',
                        'displayField' => 'name',
                        'label' => 'Город',
                        'size' => 5,
                        'id' => 'city-select',
                        'isMultiple' => false,
                        'itemDataField' => 'districts',
                        'itemDataValue' => [$model->district_id],
                        'defaultValue' => isset($cities[0]) ? [$cities[0]->id] : [],
                    ]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'district_id')->dropDownList([], [
                        'id' => 'district-select',
                        'class' => 'form-control',
                        'size' => 5,
                        'multiple' => false,
                        'style' => 'width: 100%',
                    ])->label('Район') ?>
                </div>
            </div>


            <?= $form->field($model, 'street_type_id', [
                'options' => [
                    'class' => 'form-group inline-select'
                ]
            ])->dropDownList($streetTypes, [
                'multiple' => false,
                'prompt' => '',
                'data-placeholder' => 'Тип улицы'
            ])->label('Тип улицы') ?>
        </div>
        
        <div class="col-md-6">      
            <?= $form->field($model, 'status', [
                'options' => [
                    'class' => 'form-group inline-select'
                ]
            ])->dropDownList(Newbuilding::$status, [
                'prompt' => '',
                'data-placeholder' => 'Статус'
            ]) ?>
            <?= $form->field($model, 'deadline')->widget(DatePicker::class,[
                'dateFormat' => 'dd.MM.yyyy',
                'options' => ['class'=> 'form-control'],
            ]) ?>

            <?= $form->field($model, 'street_name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'building_type_id', [
                'options' => [
                    'class' => 'form-group inline-select'
                ]
            ])->dropDownList($buildingTypes, [
                'multiple' => false,
                'prompt' => '',
                'data-placeholder' => 'Тип здания'
            ])->label('Тип здания') ?>

            <?= $form->field($model, 'building_number')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'advantages')->dropDownList($advantages, [
                'class' => 'form-control',
                'multiple' => true,
                'size' => 5,
                'style' => 'width: 100%',
            ]) ?>
        </div>
    </div>

    <?= $form->field($model, 'detail')->textarea(['rows' => 8]) ?>  

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Отмена', $backUrl, ['class' => 'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
