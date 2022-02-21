<?php
/* @var $form yii\widgets\ActiveForm */
/* @var $newbuildingComplex app\models\NewbuildingComplex */
/* @var $this yii\web\View */

use app\assets\NewbuildingComplexFormAsset;
use app\models\form\NewbuildingComplexForm;
use app\components\widgets\ImageView;
use app\components\widgets\InputFileImage;
use app\components\widgets\InputFileImages;
use app\components\widgets\InputSelect;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\TabularColumn;
use yii\widgets\Pjax;
use yii\web\View;

NewbuildingComplexFormAsset::register($this);
?>

<div class="newbuilding-complex-form">
    <?php $form = ActiveForm::begin(['id' => 'newbuilding-complex-form', 'options' => ['enctype' => 'multipart/form-data']]); ?>
    
    <div style="display: none"><?= $form->field($newbuildingComplex, 'developer_id')->hiddenInput()->label(false) ?></div>

    <div class="row">
        <div class="col-md-8">
            <?= $form->field($newbuildingComplex, 'name')->textInput(['maxlength' => true]) ?>
            <?= $form->field($newbuildingComplex, 'active',[
                'template' => "<label>{input}<span>Дом сдан</span></label>{error}",
            ])->checkbox([], false)->label(false) ?>
            
            <?= $form->field($newbuildingComplex, 'region_id', [
                'options' => [
                    'class' => 'form-group inline-select'
                ]
            ])->dropDownList($regions, [
                'id' => 'region-select',
                'prompt' => '',
                'data-placeholder' => 'Регион'
            ])->label('Регион') ?>

            <div class="row" style="margin-bottom: 0">
                <div class="col-md-6">
                    <?= InputSelect::widget([
                        'array' => $cities,
                        'checkedArray' => [$newbuildingComplex->city_id], 
                        'field' => 'NewbuildingComplexForm[city_id]',
                        'displayField' => 'name',
                        'label' => 'Город',
                        'size' => 10,
                        'id' => 'city-select',
                        'isMultiple' => false,
                        'itemDataField' => 'districts',
                        'itemDataValue' => [$newbuildingComplex->district_id],
                        'defaultValue' => isset($cities[0]) && $newbuildingComplex->scenario != NewbuildingComplexForm::SCENARIO_UPDATE ? [$cities[0]->id] : []
                    ]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($newbuildingComplex, 'district_id')->dropDownList([], [
                        'id' => 'district-select',
                        'class' => 'form-control',
                        'size' => 10,
                        'multiple' => false,
                        'style' => 'width: 100%',
                    ])->label('Район') ?>
                </div>
            </div>
            
            <?= $form->field($newbuildingComplex, 'street_type_id', [
                'options' => [
                    'class' => 'form-group inline-select'
                ]
            ])->dropDownList($streetTypes, [
                'multiple' => false,
                'prompt' => '',
                'data-placeholder' => 'Тип улицы'
            ])->label('Тип улицы') ?>

            <?= $form->field($newbuildingComplex, 'street_name')->textInput(['maxlength' => true]) ?>
            
            <?= $form->field($newbuildingComplex, 'building_type_id', [
                'options' => [
                    'class' => 'form-group inline-select'
                ]
            ])->dropDownList($buildingTypes, [
                'multiple' => false,
                'prompt' => '',
                'data-placeholder' => 'Тип здания'
            ])->label('Тип здания') ?>
            
            <?= $form->field($newbuildingComplex, 'building_number')->textInput(['maxlength' => true]) ?>

            <?= $form->field($newbuildingComplex, 'advantages')->dropDownList($advantages, [
                    'class' => 'form-control',
                    'multiple' => true,
                    'style' => 'width: 100%',
            ])->label('Преимущество') ?>

            <div id="coordinates" class="row">
                <div class="col-md-4">
                    <?= $form->field($newbuildingComplex, 'longitude')->textInput(['id' => 'longitude']) ?>
                    <?= $form->field($newbuildingComplex, 'latitude')->textInput(['id' => 'latitude']) ?>
                    <?= $form->field($newbuildingComplex, 'offer_new_price_permit', [
                        'template' => "<label>{input}<span>Возможность изменить цену КП</span></label>\n{hint}\n{error}",
                    ])->checkbox([], false)->label(false) ?>
                </div>
                
                <div class="col-md-8">
                    <div id="map"
                         data-longitude="<?= $newbuildingComplex->longitude ?>" 
                         data-latitude="<?= $newbuildingComplex->latitude ?>"
                    ></div>
                </div>
            </div>
                    
            <?= $form->field($newbuildingComplex, 'detail')->textarea(['rows' => 15]) ?>
            <?= $form->field($newbuildingComplex, 'offer_info')->textarea(['rows' => 8]) ?>
            
            <?php if($newbuildingComplex->scenario == NewbuildingComplexForm::SCENARIO_UPDATE): ?>
                <div style="margin-bottom: 30px">
                    <?= InputSelect::widget([
                        'array' => $banks, 
                        'checkedArray' => $newbuildingComplex->savedBanks, 
                        'field' => 'NewbuildingComplexForm[banks][]',
                        'displayField' => 'name',
                        'label' => 'Банки, в которых аккредитован жилой комплекс',
                        'id' => 'banks_select'
                    ]) ?>
                </div>
                <?php foreach($banks as $bank): ?>
                    <div class="bank-tariff" style="margin: 30px 0;" style="display: none;" data-bank-id="<?= $bank->id ?>">
                        <h3 style="margin: 0 0 20px;">Тарифы для <?= $bank->name ?></h3>
                        <?php if(count($bankTariffs[$bank->id]) > 0): ?>
                            <?php $tariffs = ArrayHelper::map($bankTariffs[$bank->id], 'id', 'name'); ?>
                            <?= $form->field($newbuildingComplex, "bank_tariffs[$bank->id]")->widget(MultipleInput::className(), [
                                'min' => 1,
                                'data' => isset($newbuildingComplex->bank_tariffs[$bank->id]) ? $newbuildingComplex->bank_tariffs[$bank->id] : [],
                                'allowEmptyList' => true,
                                'addButtonPosition' => MultipleInput::POS_FOOTER,
                                'attributeOptions' => [
                                    'enableAjaxValidation' => false,
                                    'enableClientValidation' => true,
                                    'validateOnChange' => true,
                                    'validateOnSubmit' => true,
                                    'validateOnBlur' => false,
                                ],
                                'columns' => [
                                    [
                                        'name' => 'name',
                                        'type' => TabularColumn::TYPE_HIDDEN_INPUT
                                    ],
                                    [
                                        'name'  => 'tariff_id',
                                        'type'  => TabularColumn::TYPE_DROPDOWN,
                                        'title' => 'Название',
                                        'items' => $tariffs,
                                        'options' => [
                                            'items' => $tariffs
                                        ],
                                        'enableError' => true,
                                    ],
                                    [
                                        'name'  => 'yearlyRateAsPercent',
                                        'type'  => TabularColumn::TYPE_TEXT_INPUT,
                                        'title' => 'Процентная ставка годовых',
                                        'enableError' => true,
                                    ],
                                    [
                                        'name'  => 'initialFeeRateAsPercent',
                                        'type'  => TabularColumn::TYPE_TEXT_INPUT,
                                        'title' => 'Первоначальный взнос',
                                        'enableError' => true,
                                    ],
                                    [
                                        'name'  => 'payment_period',
                                        'type'  => TabularColumn::TYPE_TEXT_INPUT,
                                        'title' => 'Срок ипотеки',
                                        'enableError' => true,
                                    ]
                                ]
                            ])->label(false); ?>
                        <?php else: ?>
                            <p>
                                Данные отсутсвуют
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endforeach;?>
            <?php else: ?>
                <div class="alert alert-info">
                    Возможность привязки банком будет доступна после создания ЖК
                </div>    
            <?php endif; ?>

            <?= $form->field($newbuildingComplex, 'stages')->widget(MultipleInput::className(), [
                    'min' => 0,
                    'allowEmptyList' => false,
                    'addButtonPosition' => MultipleInput::POS_FOOTER,
                    'attributeOptions' => [
                        'enableAjaxValidation' => false,
                        'enableClientValidation' => false,
                        'validateOnChange' => false,
                        'validateOnSubmit' => true,
                        'validateOnBlur' => false,
                    ],
                    'columns' => [
                        [
                            'name' => 'id',
                            'type' => TabularColumn::TYPE_HIDDEN_INPUT
                        ],
                        [
                            'name'  => 'name',
                            'type'  => TabularColumn::TYPE_TEXT_INPUT,
                            'title' => 'Этап взаимодействия',
                        ],
                        [
                            'name'  => 'description',
                            'type'  => 'textarea',
                            'title' => 'Описание',
                            'options' => [
                                'rows' => '6',
                                'style' => 'resize: none'
                            ]
                        ]
                    ]
                ])->label(false);
            ?>

            <?= $form->field($newbuildingComplex, 'master_plan')->widget(InputFileImage::className(), ['form' => $form, 'imageResetAttribute' => 'is_master_plan_reset', 'imageWidth' => '100%']) ?>

            <?= InputFileImages::widget(['form' => $form, 'model' => $newbuildingComplex, 'value' => $savedImages, 'fileField' => 'file', 'label' => 'Фото ЖК']) ?>
        </div>

        <div class="col-md-4">
            <?= $form->field($newbuildingComplex, 'logo')->widget(InputFileImage::className(), ['form' => $form, 'imageResetAttribute' => 'is_logo_reset', 'imageWidth' => '100%']) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Отмена', $backUrl, ['class' => 'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php 
$bankTariffsToJs = json_encode($bankTariffs);

$this->registerJs("
    var bankTariffs = $bankTariffsToJs;
", View::POS_BEGIN);
?>

<?= ImageView::widget() ?>
