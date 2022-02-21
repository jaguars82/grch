<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\DeveloperSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="agency-search">
    <?php $form = ActiveForm::begin([
        'id' => 'search-form-by-name',
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data' => [
                'form-name' => 'AgencySearch',
            ],
        ],
    ]); ?>

    <?= $form->field($model, 'name', [
        'template' => '
            {label}{input}
            <a href="javascript:void(0);" class="search-clear" style="display: none; margin-left: -25px">
              <i style="margin-right: 5px" class="glyphicon glyphicon-remove"></i>
            </a>
            {hint}{error}'
    ])->textInput([
        'placeholder' => 'Поиск по названию агенства недвижимости',
        'style' => 'display: inline-block; padding-right: 25px'
    ])->label(false) ?>

    <?php ActiveForm::end(); ?>
</div>
