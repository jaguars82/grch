<?php
/* @var $model app\models\NewbuildingComplex */
/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Добавить отделку';
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Застройщики', 'url' => ['admin/developer/index']];
$this->params['breadcrumbs'][] = ['label' => $newbuildingComplex->developer->name, 'url' => ['admin/developer/update', 'id' => $newbuildingComplex->developer->id]];
$this->params['breadcrumbs'][] = ['label' => 'Жилые комплексы', 'url' => ['admin/newbuilding-complex/index',  'NewbuildingComplexSearch[developer_id]' => $newbuildingComplex->developer->id]];
$this->params['breadcrumbs'][] = ['label' => $newbuildingComplex->name, 'url' => ['admin/newbuilding-complex/update', 'id' => $newbuildingComplex->id]];
$this->params['breadcrumbs'][] = ['label' => 'Отделки', 'url' => ['admin/furnish/index', 'newbuildingComplexId' => $newbuildingComplex->id]];
$this->params['breadcrumbs'][] = 'Добавить';
?>

<div class="furnish-create white-block">
    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'newbuildingComplex' => $newbuildingComplex,
        'model' =>$model,
    ]) ?>
</div>
