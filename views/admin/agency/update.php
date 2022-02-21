<?php
/* @var $model app\models\Agency */
/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Обновить агентство недвижимости: ' . $agency->name;
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Агентства недвижимости', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $agency->name, 'url' => ['update', 'id' => $agency->id]];
$this->params['breadcrumbs'][] = 'Обновить';
?>

<div class="developer-update white-block">
    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <div style="margin: 20px 0;">
        <?= Html::a('Контакты', ['admin/contact/agency-contact/index', 'agencyId' => $agency->id],  ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Администраторы агенства', ['admin/user/agency-manager/index', 'agencyId' => $agency->id],  ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Агенты', ['admin/user/agency-agent/index', 'agencyId' => $agency->id],  ['class' => 'btn btn-primary']) ?>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'backUrl' => ['index']
    ]) ?>
</div>
