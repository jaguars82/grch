<?php
/* @var $agency app\models\Agency */
/* @var $model app\models\UserForm */
/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Добавить администратора агенства ' . $agency->name;
$this->params['breadcrumbs'][] = ['label' => 'Агентства недвижимости', 'url' => ['agency/index']];
$this->params['breadcrumbs'][] = ['label' => $agency->name, 'url' => ['agency/view', 'id' => $agency->id]];
$this->params['breadcrumbs'][] = ['label' => 'Администраторы', 'url' => ['agency/view', 'id' => $agency->id]];
$this->params['breadcrumbs'][] = 'Добавить';
?>

<div class="developer-create">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('/user/_form', [
        'model' => $model,
        'redirectUrl' => $redirectUrl
    ]) ?>
</div>
