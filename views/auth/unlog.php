<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\assets\LoginAsset;

LoginAsset::register($this);

$this->title = 'Аккаунт уже используется';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-login-container">
    <div class="site-login text-center">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>Скорее всего, Ваш аккаунт используется с другого устройства.</p>
        <p>Чтобы продолжить использование сервиса на текущем устройстве, необходимо выйти из аккаунта на других устройствах.</p>
        <p>Вы можете завершить сеансы на других устройствах, нажав кнопку ниже, после чего повторить авторизацию.</p>
        
        <?php $form = ActiveForm::begin([
            'id' => 'unlog-form',
        ]); ?>
            <!--<input type="hidden" value="<?= $email ?>" />-->
            <?= $form->field($model, 'email')->hiddenInput(['value' => $email])->label(false) ?>
            <div class="unlog-btn-container">
                <?= Html::submitButton('Завершить все сеансы', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>
            <a href="/auth/login">Понятно, закрыть это сообщение</a>

        <?php ActiveForm::end(); ?>

    </div>
</div>