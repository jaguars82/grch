<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\RegionDistrict */

$this->title = 'Обновить административный район: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Города', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="region_district-update white-block">
    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
        'regions' => $regions,
        'backUrl' => Url::to(['index']),
    ]) ?>
</div>
