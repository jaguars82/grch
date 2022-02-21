<?php
/* @var $model app\models\NewbuildingComplex */
/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Обновить отделку: ' . $furnish->name;
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Застройщики', 'url' => ['admin/developer/index']];
$this->params['breadcrumbs'][] = ['label' => $furnish->newbuildingComplex->developer->name, 'url' => ['admin/developer/update', 'id' => $furnish->newbuildingComplex->developer->id]];
$this->params['breadcrumbs'][] = ['label' => 'Жилые комплексы', 'url' => ['admin/newbuilding-complex/index',  'NewbuildingComplexSearch[developer_id]' => $furnish->newbuildingComplex->developer->id]];
$this->params['breadcrumbs'][] = ['label' => $furnish->newbuildingComplex->name, 'url' => ['admin/newbuilding-complex/update', 'id' => $furnish->newbuildingComplex->id]];
$this->params['breadcrumbs'][] = ['label' => 'Отделки', 'url' => ['admin/furnish/index', 'newbuildingComplexId' => $furnish->newbuildingComplex->id]];
$this->params['breadcrumbs'][] = ['label' => $furnish->name, 'url' => ['admin/furnish/update', 'id' => $furnish->id]];
$this->params['breadcrumbs'][] = 'Обновить';
?>

<div class="newbuilding-complex-update white-block">
    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'newbuildingComplex' => $furnish->newbuildingComplex,
        'model' => $model,
    ]) ?>
</div>
