<?php
/* @var $model app\models\UserForm */
/* @var $this yii\web\View */
/* @var $user app\models\User */

use yii\helpers\Html;

$this->title = "Обновить администратора агенства: {$user->fullName}";

$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Агентства недвижимости', 'url' => ['admin/agency/index']];
$this->params['breadcrumbs'][] = ['label' => $user->agency->name, 'url' => ['admin/agency/update', 'id' => $user->agency->id]];
$this->params['breadcrumbs'][] = ['label' => 'Администраторы', 'url' => ['index', 'agencyId' => $user->agency->id]];
$this->params['breadcrumbs'][] = 'Обновить';
?>

<div class="developer-create white-block">
    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <?= $this->render('/user/_form', [
        'model' => $model,
        'redirectUrl' => $redirectUrl
    ]) ?>
</div>
