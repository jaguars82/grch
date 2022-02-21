<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Advantage */

$this->title = 'Добавить преимущество';
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Преимущества', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="advantage-create white-block">

    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
        'backUrl' => Url::to(['index'])
    ]) ?>

</div>
