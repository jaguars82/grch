<?php
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\models\Contact */
/* @var $this yii\web\View */

use app\components\widgets\ImageView;
use app\components\widgets\InputFileImage;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$dayInputsDate = [
    'Понедельник' => ['from' => 'mon_from_date', 'to' => 'mon_to_date'],
    'Вторник' => ['from' => 'tue_from_date', 'to' => 'tue_to_date'],
    'Среда' => ['from' => 'wed_from_date', 'to' => 'wed_to_date'],
    'Четверг' => ['from' => 'thu_from_date', 'to' => 'thu_to_date'],
    'Пятница' => ['from' => 'fri_from_date', 'to' => 'fri_to_date'],
    'Суббота' => ['from' => 'sat_from_date', 'to' => 'sat_to_date'],
    'Воскресенье' => ['from' => 'sun_from_date', 'to' => 'sun_to_date'],
]
?>

<div class="contact-form">
    <?php $form = ActiveForm::begin(); ?>
    
    <div style="display: none">
        <?= $form->field($model, $ownerIdName)->hiddenInput()->label(false) ?>
    </div>

    <div class="row">
        <div class="col-md-8">
            <?= $form->field($model, 'type')->textInput() ?>
            <?= $form->field($model, 'person')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
            
            <div class="">
                <?= $form->field($model, 'is_set_worktime', [
                    'template' => "<label>{input}<span>Указать время работы</span></label>{error}",
                ])->checkbox(['id' => 'is_set_worktime', 'style' => 'margin-right: 5px', 'prompt' => '',], false)->label(false) ?>
            </div>
            
            <div class="form-group collapse <?php if($model->is_set_worktime): ?>in<?php endif?>" id="worktime">
                <?php foreach($dayInputsDate as $dayName => $dayInputs): ?>
                <div class="row" >
                    <div class="col-xs-4 col-xs-3" style="padding-right: 0;">
                        <label style="vertical-align: middle; line-height: 34px;" for="<?= "contactform-{$dayInputs['from']}" ?>"><?= $dayName ?></label>
                    </div>
                    <div class="col-xs-4 col-sm-3" style="padding-right: 0;">
                        <?= $form->field($model, $dayInputs['from'], [
                            'template' => "
                                {label}
                                <div class=\"input-group\">
                                    <div class=\"input-group-addon\">
                                        <i class=\"glyphicon glyphicon-time\"></i>
                                    </div>
                                    {input}
                                </div>
                                {hint}{error}"
                        ])->textInput(['maxlength' => true])->label(false) ?>
                    </div>
                    <div class="text-center" style="vertical-align: middle; line-height: 34px; float: left; width: 20px">
                        -
                    </div>
                    <div class="col-xs-4 col-sm-3" style="padding-left: 0;">
                        <?= $form->field($model, $dayInputs['to'], [
                            'template' => "
                                {label}
                                <div class=\"input-group\">
                                    <div class=\"input-group-addon\">
                                        <i class=\"glyphicon glyphicon-time\"></i>
                                    </div>
                                    {input}
                                </div>
                                {hint}{error}"
                        ])->textInput(['maxlength' => true])->label(false) ?>
                    </div>
                </div>
                <?php endforeach ?>
            </div>            
        </div>
        
        <div class="col-md-4">
            <?= $form->field($model, 'photo')->widget(InputFileImage::className(), ['form' => $form, 'imageResetAttribute' => 'is_phone_reset', 'imageWidth' => '100%']) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>        
        <?= Html::a('Отмена', $backUrl, ['class' => 'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?= ImageView::widget() ?>