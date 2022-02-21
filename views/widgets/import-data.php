<?php
use app\assets\widgets\ImportDataAsset;
use app\models\Import;
use app\models\form\ImportDataForm;

ImportDataAsset::register($this);
?>

<?= $form->field($model, 'type', [
    'options' => [
        'class' => 'form-group inline-select'
    ]
])->dropDownList(Import::$import_types, [
    'id' => 'import-type',
    'data-import-types' => implode(',', Import::$import_auto),
    'data-placeholder' => 'Тип импорта',
]) ?>
<div id="import-algorithm" >
    <?= $form->field($model, 'algorithm')->textInput(); ?>
</div>

<div id="import-data" >
    <?= $form->field($model, 'endpoint')->textInput(['maxlength' => true]) ?>
    
    <!--div clas="row"-->
        <!--div class="col-md-6" style="padding-left: 0"-->
            <!--?= $form->field($model, 'period')->textInput(); ?-->
        <!--/div-->
        
        <!--div class="col-md-6" style="padding-right: 0"-->
            <!--?= $form->field($model, 'period_type')->dropDownList(array_merge(['' => ''], ImportDataForm::$periodType)); ?-->
        <!--/div-->
    <!--/div-->
</div>