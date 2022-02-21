<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\BuildingType */

$this->title = 'Добавить тип здания';
$this->params['breadcrumbs'][] = ['label' => 'Типы зданий', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="building-type-create white-block">

    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
        'backUrl' => Url::to(['index'])
    ]) ?>

</div>
