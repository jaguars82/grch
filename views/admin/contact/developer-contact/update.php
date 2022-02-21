<?php
/* @var $model app\models\Contact */
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Обновить контакт: ' . $contact->person;
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Застройщики', 'url' => ['admin/developer/index']];
$this->params['breadcrumbs'][] = ['label' => $developer->name, 'url' => ['admin/developer/update', 'id' => $developer->id]];
$this->params['breadcrumbs'][] = ['label' => 'Контакты', 'url' => ['index', 'developerId' => $developer->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="contact-update white-block">
    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <?= $this->render('/contact/_form', [
        'model' => $model,
        'ownerIdName' => 'developer_id',
        'backUrl' => Url::to(['index', 'developerId' => $developer->id]),
    ]) ?>
</div>
