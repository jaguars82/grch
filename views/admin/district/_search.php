<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\DistrictSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="district-search">

    <?php $form = ActiveForm::begin([
        'id' => 'search-form-district-index',
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data' => [
                'form-name' => 'DistrictSearch',
            ],
        ],
    ]); ?>
    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'city_id')->dropDownList($cities, ['prompt' => 'Город'])->label(false) ?>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <?= Html::submitButton('Найти', ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
