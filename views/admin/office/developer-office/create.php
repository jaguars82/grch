<?php

/* @var $this yii\web\View */
/* @var $model app\models\Office */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = "Добавить офис для застройщика {$developer->name}";
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Застройщики', 'url' => ['admin/developer/index']];
$this->params['breadcrumbs'][] = ['label' => $developer->name, 'url' => ['admin/developer/update', 'id' => $developer->id]];
$this->params['breadcrumbs'][] = ['label' => 'Офисы', 'url' => ['index', 'developerId' => $developer->id]];
$this->params['breadcrumbs'][] = 'Добавить';
?>

<div class="office-create white-block">
    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
        'ownerIdName' => 'developer_id',
        'backUrl' => Url::to(['index', 'developerId' => $developer->id]),
    ]) ?>
</div>
