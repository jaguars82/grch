<?php
/* @var $agency app\models\Agency */
/* @var $model app\models\UserForm */
/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Добавить администратора агенства ' . $agency->name;
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Агентства недвижимости', 'url' => ['admin/agency/index']];
$this->params['breadcrumbs'][] = ['label' => $agency->name, 'url' => ['admin/agency/update', 'id' => $agency->id]];
$this->params['breadcrumbs'][] = ['label' => 'Администраторы', 'url' => ['index', 'agencyId' => $agency->id]];
$this->params['breadcrumbs'][] = 'Добавить';
?>

<div class="developer-create white-block">
    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <?= $this->render('/user/_form', [
        'model' => $model,
        'redirectUrl' => $redirectUrl
    ]) ?>
</div>
