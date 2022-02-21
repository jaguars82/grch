<?php
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $this yii\web\View */

use app\assets\CreditCalculationAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\View;
use yii\helpers\ArrayHelper;

$this->title = "Рассчитать кредит для банка \"{$bank->name}\"";
$this->params['breadcrumbs'][] = ['label' => 'Банки', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $bank->name, 'url' => ['view', 'id' => $bank->id]];
$this->params['breadcrumbs'][] = 'Рассчитать кредит';

CreditCalculationAsset::register($this);
?>
<div class="bank-index calculation white-block">
    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'tariff_id', [
                'options' => [
                    'class' => 'form-group inline-select'
                ]
            ])->dropDownList(ArrayHelper::map($tariffs, 'tariff_id', 'name'), [
                'prompt' => '',
                'data-placeholder' => 'Тариф',
            ]); ?>

            <?= $form->field($flat, is_null($flat->price_credit) ? 'cashPriceWithDiscount' : 'creditPriceWithDiscount', [
                'template' => '
                    {label}
                    <div class="input-group">
                        {input}
                        <div class="input-group-addon">руб.</div>
                    </div>
                    {hint}{error}'
            ])->textInput(['id' => 'cost', 'readonly' => true])->label('Стоимость квартиры') ?>

            <?= $form->field($model, 'yearlyRateAsPercent', [
                'template' => '
                    {label}
                    <div class="input-group">
                        {input}
                        <div class="input-group-addon"><span style="width: 26px; display: inline-block">%</span></div>
                    </div>
                    {hint}{error}'
            ])->textInput(['id' => 'yearlyRate', 'readonly' => true]) ?>
            
            <div class="form-group">
                <label class="control-label" >Первоначальный взнос</label><br>
                <div class="input-group">
                    <input type="text" id="initialFee" class="form-control box-shadow" value="<?= $tariffs[$model->tariff_id]['initialFee'] ?>">
                    <div class="input-group-addon">руб.</div>
                </div>
            </div>

            <?= $form->field($model, 'payment_period', [
                'template' => '
                    {label}
                    <div class="input-group">
                        {input}
                        <div class="input-group-addon">мес.</div>
                    </div>
                    {hint}{error}'
            ])->textInput(['id' => 'time']) ?>            
        </div>
        
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label" >Ежемесячный платеж</label><br>
                <div class="input-group">
                    <input type="text" id="monthlyPayment" class="form-control box-shadow" readonly>
                    <div class="input-group-addon">руб.</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <?= Html::a('Отмена', ['flat/view', 'id' => $flat->id], ['class' => 'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php 
$tariffsToJs = json_encode($tariffs);

$this->registerJs("
    var tariffs = $tariffsToJs;
", View::POS_BEGIN);
?>