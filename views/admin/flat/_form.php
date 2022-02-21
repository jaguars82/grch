<?php
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\models\Flat */
/* @var $this yii\web\View */

use app\components\widgets\FloorLayout;
use app\components\widgets\ImageView;
use app\components\widgets\InputFileImage;
use app\components\widgets\InputFileImages;
use app\components\widgets\InputSelect;
use app\models\Flat;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="flat-form">
    <?php $form = ActiveForm::begin(); ?>    
    
    <div style="display: none"><?= $form->field($model, 'newbuilding_id')->hiddenInput()->label(false)?></div>

    <div class="row">        
        <div class="col-md-4">
            <?= $form->field($model, 'layout_type', [
                'options' => [
                    'class' => 'form-group inline-select'
                ]
            ])->dropDownList([
                'euro' => 'Евро',
                'studio' => 'Студия'
            ], [
                'prompt' => '',
                'data-placeholder' => 'Планировка'
            ]) ?>
            <?= $form->field($model, 'number')->textInput(['maxlength' => true]) ?> 
            <?= $form->field($model, 'floor')->textInput() ?>
            <?= $form->field($model, 'section')->textInput() ?>            
            <?= $form->field($model, 'azimuth', [
                'template' => '
                    {label}
                    <div class="input-group">
                        {input}
                        <div class="input-group-addon">°</div>
                    </div>
                    {hint}{error}'
            ])->textInput() ?>
        </div>
        
        <div class="col-md-4">
            <?= $form->field($model, 'status', [
                'options' => [
                    'class' => 'form-group inline-select'
                ]
            ])->dropDownList(Flat::$status, [
                'data-placeholder' => 'Статус',
                'prompt' => '',
                'class' => 'form-control flat-status',
                'data-colors' => json_encode(array_values(\app\components\widgets\FloorLayout::COLORS)),
            ]) ?>

            <?= $form->field($model, 'unit_price_cash', [
                'template' => '
                    {label}
                    <div class="input-group">
                        {input}
                        <div class="input-group-addon">руб.</div>
                    </div>
                    {hint}{error}'
            ])->textInput(['maxlength' => true]) ?>
            
            <?= $form->field($model, 'price_cash', [
                'template' => '
                    {label}
                    <div class="input-group">
                        {input}
                        <div class="input-group-addon">руб.</div>
                    </div>
                    {hint}{error}'
            ])->textInput(['maxlength' => true]) ?>
            
            <?= $form->field($model, 'unit_price_credit', [
                'template' => '
                    {label}
                    <div class="input-group">
                        {input}
                        <div class="input-group-addon">руб.</div>
                    </div>
                    {hint}{error}'
            ])->textInput(['maxlength' => true]) ?>
            
            <?= $form->field($model, 'price_credit', [
                'template' => '
                    {label}
                    <div class="input-group">
                        {input}
                        <div class="input-group-addon">руб.</div>
                    </div>
                    {hint}{error}'
            ])->textInput(['maxlength' => true]) ?>
            
        </div>
        
        <div class="col-md-4">
            <?= $form->field($model, 'discountAsPercent', [
                'template' => '
                    {label}
                    <div class="input-group">
                        {input}
                        <div class="input-group-addon">%</div>
                    </div>
                    {hint}{error}'
            ])->textInput() ?>
            
            <?= $form->field($model, 'area', [
                'template' => '
                    {label}
                    <div class="input-group">
                        {input}
                        <div class="input-group-addon">кв.м.</div>
                    </div>
                    {hint}{error}'
            ])->textInput() ?>

            <?= $form->field($model, 'rooms')->textInput() ?>
            
            <?php if(!is_null($flat)): ?>
                <div style="display: none">
                    <?= $form->field($model, 'floor_position', [
                        'template' => '
                            {label}
                            <div class="input-group">
                                <div class="input-group-addon"><a href="#" class="floor-layout" data-viewer-id="' . $flat->id .'"><i class="glyphicon glyphicon-map-marker"></i></a></div>
                                {input}                        
                            </div>
                            {hint}{error}'
                    ])->textInput(['class' => 'form-control floor-position-input']) ?>
                </div>
            
                <?php if(!is_null($flat->floorLayout) && !is_null($flat->floorLayout->image) && !empty($flat->floorLayout->map)): ?>
                    <?= Html::a('Выбрать позицию на планировке этажа', '#', ['class' => 'floor-layout btn btn-primary', 'data-viewer-id' => $flat->id]) ?>
                <?php endif ?>
            <?php endif ?>
        </div>        
    </div>
    
    <?= $form->field($model, 'notification')->textarea(['rows' => 5]) ?>    
    <?= $form->field($model, 'detail')->textarea(['rows' => 5]) ?>
    
    <div style="margin-bottom: 20px">
        <?= InputSelect::widget([
            'array' => $actions, 
            'checkedArray' => $model->savedActions, 
            'field' => 'FlatForm[actions][]',
            'displayField' => 'resume',
            'label' => 'Акции применяемые к квартире'
        ]) ?>
    </div>
    
    <div class="row">
        <div class="col-md-3" style="margin-bottom: 20px">        
            <?= $form->field($model, 'layout')->widget(InputFileImage::className(), ['form' => $form, 'imageResetAttribute' => 'is_layout_reset', 'imageWidth' => '100%']) ?>
        </div>
    </div>
    
    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Отмена', $backUrl, ['class' => 'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>