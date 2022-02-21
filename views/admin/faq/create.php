<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Faq */

$this->title = 'Добавить запись';
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Помощь', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="faq-create white-block">

    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
        'backUrl' => Url::to(['index']),
    ]) ?>

</div>
