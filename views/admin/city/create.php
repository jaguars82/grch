<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\City */

$this->title = 'Добавить город';
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Города', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="city-create white-block">

    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
        'regions' => $regions,
        'region_districts' => $region_districts,
        'backUrl' => Url::to(['index']),
    ]) ?>

</div>
