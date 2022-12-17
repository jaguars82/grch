<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\assets\LoginAsset;

LoginAsset::register($this);

$this->title = 'Авторизация';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-login-container">
    <div class="site-login text-center">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>Заполните следующие поля, чтобы войти на сайт:</p>

        <div id="login-form-code" class="
        <?php if ($defaultLogin === 'pass'): ?>
            hidden
        <?php endif; ?>
        ">
            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
                'layout' => 'horizontal',
                'fieldConfig' => [
                    'template' => "<div class=\"text-left\">{label}</div>\n<div>{input}</div>",
                    'labelOptions' => ['class' => 'control-label'],
                ],
            ]); ?>

            <?= $form->field($model, 'loginway')->hiddenInput(['value' => 'otp'])->label(false) ?>
            <?= $form->field($model, 'password')->hiddenInput(['value' => 'no'])->label(false) ?>

            <?= $form->field($model, 'email')->textInput(['autofocus' => true, 'class' => 'form-control email-otp'])->label('E-mail') ?>

            <?= $form->field($model, 'otp', ['template' => "<div class=\"code-otp text-left\">{label}\n<div>{input}</div>\n</div>"])->textInput()->label('Код') ?>

            <div class="form-group js-otp">
                <div>
                    <?= Html::button('Отправить код на e-mail', ['class' => 'btn btn-primary js-send-code']) ?>
                </div>
            </div>

            <div class="form-group login-button js-login">
                <div>
                    <?= Html::submitButton('Войти', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>

        <div id="login-form-pass" class="
        <?php if ($defaultLogin === 'otp'): ?>
            hidden
        <?php endif; ?>
        ">
            <?php $form = ActiveForm::begin([
                'id' => 'login-form-pass',
                'layout' => 'horizontal',
                'fieldConfig' => [
                    'template' => "<div class=\"text-left\">{label}</div>\n<div>{input}</div>",
                    'labelOptions' => ['class' => 'control-label'],
                ],
                'validateOnSubmit' => false,
            ]); ?>

            <?= $form->field($model, 'loginway')->hiddenInput(['value' => 'pass'])->label(false) ?>
            <?= $form->field($model, 'otp')->hiddenInput(['value' => 'no'])->label(false) ?>

            <?= $form->field($model, 'email')->textInput(['autofocus' => true, 'class' => 'form-control email-otp'])->label('E-mail') ?>

            <?= $form->field($model, 'password', ['template' => "<div class=\"text-left\">{label}\n<div>{input}</div>\n</div>"])->passwordInput()->label('Пароль') ?>

            <div class="form-group">
                <div>
                    <?= Html::submitButton('Войти', ['id' => 'pass-login-button', 'class' => 'btn btn-primary', 'name' => 'pass-login-button']) ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>

        <div>
            <button id="toggle-to-code" class="login-toggle-btn
            <?php if ($defaultLogin === 'otp'): ?>    
                active
            <?php endif; ?>
            ">
                Вход по коду
            </button>
            <button id="toggle-to-pass" class="login-toggle-btn
            <?php if ($defaultLogin === 'pass'): ?>    
                active
            <?php endif; ?>
            ">
                Вход по паролю
            </button>
        </div>

    </div>
</div>