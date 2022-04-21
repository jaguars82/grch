<?php
/* @var $agency app\models\Agency */
/* @var $model app\models\UserForm */
/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Добавить агента ' . $agency->name;
$this->params['breadcrumbs'][] = ['label' => 'Кабинет пользователя', 'url' => ['user/profile']];
$this->params['breadcrumbs'][] = ['label' => 'Агенты "'.$agency->name.'"', 'url' => ['user/agency-agent/', 'agencyId' => $agency->id]];
$this->params['breadcrumbs'][] = 'Добавить агента';
?>

<div class="row">
    
    <div class="col-md-3">

    <?= $this->render('/user/_sideblock') ?>

    </div>

    <div class="col-md-9">
        <div class="white-block">


            <div class="agent-create">
                <h1><?= Html::encode($this->title) ?></h1>

                <?= $this->render('/user/_form', [
                    'model' => $model,
                    'redirectUrl' => $redirectUrl
                ]) ?>
            </div>

        </div>
    </div>
    
</div>
                
