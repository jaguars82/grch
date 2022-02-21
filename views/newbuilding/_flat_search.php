<?php

use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\NewbuildingComplexFlatSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="newbuilding-complex-flat-search">
    <?php $form = ActiveForm::begin([
        'id' => 'search-newbuilding-flats',
        'action' => ['view'],
        'method' => 'get',
        'options' => [
            'data' => [
                'developer_id' => $newbuilding->newbuildingComplex->developer_id,
                'newbuilding_complex_id' => $newbuilding->newbuilding_complex_id,
                'newbuilding_id' => $newbuilding->id
            ],
        ]        
    ]); ?>
    
    <?= $form->field($model, 'rooms')->dropDownList([1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => '5+'], ['prompt' => 'Количество комнат'])->label(false) ?>
    
    <div class="form-group">
        <div class="col-md-12 control-label" style="padding-left: 0;">
            <label>Цена</label>
        </div>

        <div class="col-md-6" style="padding-left: 0; padding-right: 5px;">
            <?= $form->field($model, 'priceFrom')->textInput(['placeholder' => 'От'])->label(false) ?>
        </div>

        <div class="col-md-6" style="padding-left: 5px; padding-right: 0;">
            <?= $form->field($model, 'priceTo')->textInput(['placeholder' => 'До'])->label(false) ?>
        </div>
    </div>
    
    <div class="form-group">
        <div class="col-md-12 control-label" style="padding-left: 0;">
            <label>Площадь</label>
        </div>

        <div class="col-md-6" style="padding-left: 0; padding-right: 5px;">
            <?= $form->field($model, 'areaFrom')->textInput(['placeholder' => 'От'])->label(false) ?>
        </div>

        <div class="col-md-6" style="padding-left: 5px; padding-right: 0;">
            <?= $form->field($model, 'areaTo')->textInput(['placeholder' => 'До'])->label(false) ?>
        </div>
    </div>
    
    <div class="form-group">
        <div class="col-md-12 control-label" style="padding-left: 0;">
            <label>Этажи</label>
        </div>

        <div class="col-md-6" style="padding-left: 0; padding-right: 5px;">
            <?= $form->field($model, 'floorFrom')->textInput(['placeholder' => 'От'])->label(false) ?>
        </div>

        <div class="col-md-6" style="padding-left: 5px; padding-right: 0;">
            <?= $form->field($model, 'floorTo')->textInput(['placeholder' => 'До'])->label(false) ?>
        </div>
    </div>
    
    <div class="form-group">
        <?= Html::a('Дополнительные параметры', '#flat-search-extra', ['class' => 'btn btn-primary', 'data-toggle' => 'collapse'])?>
        
        <div class="collapse" id="flat-search-extra" style="margin-top: 15px">
            <?= $form->field($model, 'update_date')->widget(DatePicker::class,[
                'dateFormat' => 'dd.MM.yyyy',
                'options' => [
                    'class'=> 'form-control',
                ],
            ]) ?>
        </div>
    </div>
    
    <div class="form-group" style="margin-bottom: 0">
        <?= Html::submitButton('Поиск', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Очистить', '#', ['id' => 'flat-search-clear', 'class' => 'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
