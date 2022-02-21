<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Авторизация';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Заполните следующие поля, чтобы войти на сайт:</p>

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]); ?>

    <?= $form->field($model, 'email')->textInput(['autofocus' => true, 'class' => 'form-control email-otp'])->label('E-mail') ?>

    <?= $form->field($model, 'otp', ['template' => "<div class=\"code-otp\">{label}\n<div class=\"col-lg-3\">{input}</div>\n</div>"])->textInput()->label('Код') ?>

    <div class="form-group js-otp">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::button('Отправить код на e-mail', ['class' => 'btn btn-primary js-send-code']) ?>
        </div>
    </div>

    <div class="form-group login-button js-login">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton('Войти', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>