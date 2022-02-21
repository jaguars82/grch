<?php
/* @var $model app\models\UserForm */
/* @var $this yii\web\View */
/* @var $user app\models\User */

use yii\helpers\Html;

$this->title = "Обновить администратора агенства: {$user->fullName}";
$this->params['breadcrumbs'][] = ['label' => 'Агентства недвижимости', 'url' => ['agency/index']];
$this->params['breadcrumbs'][] = ['label' => $user->agency->name, 'url' => ['agency/view', 'id' => $user->agency->id]];
$this->params['breadcrumbs'][] = ['label' => 'Администраторы', 'url' => ['agency/view', 'id' => $user->agency->id]];
$this->params['breadcrumbs'][] = ['label' => $user->fullName, 'url' => ['agency-manager/view', 'id' => $user->id]];
$this->params['breadcrumbs'][] = 'Обновить';
?>

<div class="developer-create">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('/user/_form', [
        'model' => $model,
        'redirectUrl' => $redirectUrl
    ]) ?>
</div>
