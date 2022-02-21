<?php
/* @var $model app\models\Agency */
/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Добавить агентства недвижимости';
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Агентства недвижимости', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Добавить';
?>

<div class="developer-create white-block">
    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
        'backUrl' => ['index']
    ]) ?>
</div>
