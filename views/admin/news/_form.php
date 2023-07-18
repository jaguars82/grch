<?php
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\models\News */
/* @var $this yii\web\View */

use app\assets\NewsFormAsset;
use app\components\widgets\ImageView;
use app\components\widgets\InputFileImage;
use app\components\widgets\InputFiles;
use app\components\widgets\InputSelect;
use app\models\News;
use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

NewsFormAsset::register($this);
?>

<div class="news-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-8">
            <?= $form->field($news, 'title')->textInput(['maxlength' => true]) ?>
            <?php // echo '<pre>'; var_dump($action); echo '<pre>'; die(); ?>
            <div id="actions-data" <?= $news->category === News::CATEGORY_ACTION ? '' : 'style="display: none"' ?>>
                <div style="display: none">
                    <?= $form->field($action, 'is_enabled')->checkbox(['class' => 'action-is-enabled', 'checked' => $news->category === News::CATEGORY_ACTION]) ?>
                </div>

                <?= $form->field($action, 'resume')->textInput(['maxlength' => true]) ?>
                <!-- discount block -->
                <div class="row">
                    <div class="col-md-12">
                        <h4 style="font-weight: 400;">Размер скидки</h4>
                        <?= $form->field($action, 'discount_type')->hiddenInput(['id' => 'discount_type'])->label(false) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($action, 'discount')->textInput(['id' => 'discount_percent', 'maxlength' => true, 'placeholder' => 'В процентах'])->label(false) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($action, 'discount_amount')->textInput(['id' => 'discount_amount', 'maxlength' => true, 'placeholder' => 'В рублях'])->label(false) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($action, 'discount_price')->textInput(['id' => 'discount_price', 'maxlength' => true, 'placeholder' => 'Цена со скидкой'])->label(false) ?>
                    </div>
                </div>
                <?= $form->field($action, 'expired_at')->widget(DatePicker::class,[
                    'dateFormat' => 'dd.MM.yyyy',
                    'options' => ['class'=> 'form-control'],
                ]) ?>

                <a href="#" class="btn btn-primary action-flat-search-show">Параметры для выбора квартир</a>

                <?php if ($news->category === News::CATEGORY_ACTION || $news->title == ''): ?>
                <?= $this->render('_action_flat_search', [
                    'model' => $search,
                    'districts' => $districts,
                    'developers' => $developersSearch,
                    'newbuildingComplexes' => $newbuildingComplexes,
                    'newbuildings' => $newbuildings,
                    'entrances' => $entrances,
                    'index_on_floor' => $index_on_floor,
                    'numbers' => $numbers,
                    'positionArray' => $positionArray,
                    'materials' => $materials,
                    'form' => $form,
                ]) ?>
            <?php endif; ?>
            </div>

            <?= $form->field($news, 'detail')->textarea(['rows' => 10]) ?>

            <div class="row" style="margin-bottom: 0">
                <div class="col-md-6">
                    <?= InputSelect::widget([
                        'array' => $developers,
                        'checkedArray' => [$news->developerId],
                        'field' => 'NewsForm[developerId]',
                        'displayField' => 'name',
                        'label' => 'Застройщик',
                        'size' => 10,
                        'id' => 'developer-select',
                        'isMultiple' => false,
                        'itemDataField' => 'newbuilding-complexes',
                        'itemDataValue' => $news->newbuildingComplexes,
                    ]) ?>
                </div>

                <div class="col-md-6">
                    <?= $form->field($news, 'newbuildingComplexes[]')->dropDownList([], [
                        'id' => 'newbuilding-complex-select1',
                        'class' => 'form-control',
                        'size' => 10,
                        'multiple' => true,
                        'style' => 'width: 100%',
                    ]) ?>
                </div>
            </div>

            <?= $form->field($news, 'search_link')->textarea(['rows' => 5]) ?>

            <?= InputFiles::widget(['form' => $form, 'model' => $news]) ?>
        </div>

        <div class="col-md-4">
            <?= $form->field($news, 'category', [
                'options' => [
                    'class' => 'form-group inline-select'
                ]
            ])->dropDownList(News::$category, [
                'prompt' => '',
                'data-placeholder' => 'Категория'
            ]) ?>
            <?= $form->field($news, 'image')->widget(InputFileImage::className(), ['form' => $form, 'imageResetAttribute' => 'is_image_reset', 'imageWidth' => '100%']) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Отмена', $backUrl, ['class' => 'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?= ImageView::widget() ?>