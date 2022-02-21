<?php
/* @var $this yii\web\View */
/* @var $model app\models\FloorLayout */
/* @var $form yii\widgets\ActiveForm */

use app\components\widgets\ImageView;
use app\components\widgets\InputFileImage;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="floor-layout-form">
    <?php $form = ActiveForm::begin(); ?>

    <div style="display: none">
        <input id="floor_layout_id" value="<?= $model->floorLayoutId ?>">
        <?= $form->field($model, 'newbuilding_id')->hiddenInput()->label(false) ?>
    </div>
    
    <div class="row">
        <div class="col-md-2">
            <?= $form->field($model, 'floor')->textInput() ?>       
        </div>

        <div class="col-md-2">
            <?= $form->field($model, 'section')->textInput() ?>       
        </div>

        <div class="col-md-12">
            <?= $form->field($model, 'map')->textarea(['rows' => 6]) ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'image')->widget(InputFileImage::className(), ['form' => $form, 'imageWidth' => '100%']) ?>
        </div>
    </div>

    <div class="form-group" style="margin-top: 10px">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Отмена', ['admin/floor-layout/index', 'newbuildingId' => $model->newbuilding_id], ['class' => 'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?= ImageView::widget() ?>
