<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\District */

$this->title = 'Обновить район: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Районы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="district-update white-block">

    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
        'cities' => $cities,
        'backUrl' => Url::to(['admin/district/index'])
    ]) ?>

</div>
