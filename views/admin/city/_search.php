<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\DistrictSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="city-search">

    <?php $form = ActiveForm::begin([
        'id' => 'search-form-city-index',
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data' => [
                'form-name' => 'CitySearch',
            ],
        ],
    ]); ?>
    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'region_id',[
                'options' => [
                    'class' => 'form-group inline-select'
                ]
            ])->dropDownList($regions, [
                'prompt' => '',
                'data-placeholder' => 'Регион'
            ])->label(false) ?>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <?= Html::submitButton('Найти', ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
