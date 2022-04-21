<?php
/* @var $model app\models\UserForm */
/* @var $this yii\web\View */
/* @var $user app\models\User */

use yii\helpers\Html;

$this->title = "Обновить агента {$user->fullName}";
$this->params['breadcrumbs'][] = ['label' => 'Кабинет пользователя', 'url' => ['user/profile']];
$this->params['breadcrumbs'][] = ['label' => 'Агенты "'.$user->agency->name.'"', 'url' => ['user/agency-agent/', 'agencyId' => $user->agency->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    
    <div class="col-md-3">

    <?= $this->render('/user/_sideblock') ?>

    </div>

    <div class="col-md-9">
        <div class="white-block">

            <div class="developer-create">
                <h1><?= Html::encode($this->title) ?></h1>

                <?= $this->render('/user/_form', [
                    'model' => $model,
                    'redirectUrl' => $redirectUrl
                ]) ?>
            </div>
        
        </div>
    </div>

</div>
