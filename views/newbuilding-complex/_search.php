<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\NewbuildingComplexSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="newbuilding-complex-search">
    <?php $form = ActiveForm::begin([
        'id' => 'search-form-newbuilding-index',
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data' => [
                'form-name' => 'NewbuildingComplexSearch',
            ],
        ],
    ]); ?>

    <div class="row flex-row">
        <div class="col-xs-12 col-sm-3 col-md-3">
            <?= $form->field($model, 'developer_id', [
                'options' => [
                    'class' => 'form-group inline-select'
                ]
            ])->dropDownList($developers, [
                'prompt' => '',
                'data-placeholder' => 'Застройщик',
            ])->label(false) ?>
        </div>
        
        <div class="col-xs-12 col-sm-4 col-md-5">
            <?= $form->field($model, 'name')->textInput([
                'placeholder' => 'Название жилого комплекса',
            ])->label(false) ?>
        </div>
        
        <div class="col-xs-12 col-sm-5 col-md-4">
            <!--<?= $form->field($model, 'only_active', [
                'template' => "<label>{input}<span>Только с активными предложениями</span></label>{error}",
            ])->checkbox([], false)->label(false) ?>-->
            <div>
                <?= Html::submitButton('Поиск', [
                    'class' => 'btn btn-primary',
                    'style' => 'margin-bottom: 15px;'
                ]);?>
            </div>
        </div>


    </div>
    


    <?php ActiveForm::end(); ?>
</div>
