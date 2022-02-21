<?php
/* @var $model app\models\Contact */
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = "Добавить контакт для агенства недвижимости {$agency->name}";
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Агентства недвижимости', 'url' => ['admin/agency/index']];
$this->params['breadcrumbs'][] = ['label' => $agency->name, 'url' => ['admin/agency/update', 'id' => $agency->id]];
$this->params['breadcrumbs'][] = ['label' => 'Контакты', 'url' => ['index', 'agencyId' => $agency->id]];
$this->params['breadcrumbs'][] = 'Добавить';
?>

<div class="contact-create white-block">
    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <?= $this->render('/contact/_form', [
        'model' => $model,
        'ownerIdName' => 'agency_id',
        'backUrl' => Url::to(['index', 'agencyId' => $agency->id]),
    ]) ?>
</div>
