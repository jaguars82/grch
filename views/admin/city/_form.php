<?php

use app\assets\admin\CityFormAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\widgets\InputAddress;

/* @var $this yii\web\View */
/* @var $model app\models\City */
/* @var $form yii\widgets\ActiveForm */

CityFormAsset::register($this);
?>

<div class="city-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">

        <div class="col-md-6">
        
            <?= $form->field($model, 'region_id', [
                'options' => [
                    'class' => 'form-group inline-select'
                ]
            ])->dropDownList($regions, [
                'prompt' => '',
                'data-placeholder' => 'Регион'
            ])->label(false) ?>

        </div>

        <div class="col-md-6">
        
            <?= $form->field($model, 'region_district_id', [
                'options' => [
                    //'id' => 'region-district-select',
                    'class' => 'form-group inline-select'
                ]
            ])->dropDownList($region_districts, [
                'prompt' => '',
                'data-placeholder' => 'Район'
            ])->label(false) ?>

        </div>

        <div class="col-md-12">

            <?= $form->field($model, 'name')->widget(InputAddress::className(), ['form' => $form, 'attribute' => 'name']) ?>

        </div>

        <div class="col-md-6">
        
            <?= $form->field($model, 'is_region_center',[
                        'template' => "<label>{input}<span>Региональный центр</span></label>{error}",
                    ])->checkbox([], false)->label(false) ?>

        </div>

        <div class="col-md-6">

            <?= $form->field($model, 'is_district_center',[
                        'template' => "<label>{input}<span>Районный центр</span></label>{error}",
                    ])->checkbox([], false)->label(false) ?>

        </div>

        <div class="col-md-12">

            <div class="form-group">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
                <?= Html::a('Отмена', $backUrl, ['class' => 'btn btn-danger']) ?>
            </div>

        </div>

    </div>

    <?php ActiveForm::end(); ?>

</div>
