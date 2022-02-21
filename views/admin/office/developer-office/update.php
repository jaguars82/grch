<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Office */

$this->title = 'Обновить офис: ' . $office->name;
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Застройщики', 'url' => ['admin/developer/index']];
$this->params['breadcrumbs'][] = ['label' => $office->developer->name, 'url' => ['admin/developer/update', 'id' => $office->developer->id]];
$this->params['breadcrumbs'][] = ['label' => 'Офисы', 'url' => ['index', 'developerId' => $office->developer->id]];
$this->params['breadcrumbs'][] = ['label' => $office->name, 'url' => ['update', 'id' => $office->id]];
$this->params['breadcrumbs'][] = 'Обновить';
?>
<div class="office-update white-block">

    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
        'backUrl' => Url::to(['index', 'developerId' => $office->developer->id]),
    ]) ?>

</div>
