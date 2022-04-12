<?php

use yii\helpers\Html;

$this->title = 'Редактировать данные профиля';
$this->params['breadcrumbs'][] = ['label' => 'Кабинет пользователя', 'url' => ['user/profile/index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-md-12">

        <div class="white-block">
            <h1 class="my-100"><?= Html::encode($this->title) ?></h1>

            <?= $this->render('/user/_form', [
                'model' => $model,
                'redirectUrl' => $redirectUrl
            ]) ?>
        </div>

    </div>
</div>