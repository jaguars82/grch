<?php
/* @var $model app\models\Contact */
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Обновить контакт: ' . $contact->person;
$this->params['breadcrumbs'][] = ['label' => 'Агентства недвижимости', 'url' => ['agency/index']];
$this->params['breadcrumbs'][] = ['label' => $agency->name, 'url' => ['agency/view', 'id' => $agency->id]];
$this->params['breadcrumbs'][] = ['label' => 'Контакты', 'url' => ['agency/view', 'id' => $agency->id]];
$this->params['breadcrumbs'][] = ['label' => $contact->person];
$this->params['breadcrumbs'][] = 'Обновить';
?>

<div class="contact-update">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('/contact/_form', [
        'model' => $model,
        'ownerIdName' => 'agency_id',
        'backUrl' => Url::to(['agency/view', 'id' => $agency->id]),
    ]) ?>
</div>
