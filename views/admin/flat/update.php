<?php
/* @var $model app\models\Flat */
/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = "Обновить квартиру: {$newbuilding->address}, №{$model->number}";
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Застройщики', 'url' => ['admin/developer/index']];
$this->params['breadcrumbs'][] = ['label' => $newbuilding->developer->name, 'url' => ['admin/developer/update', 'id' => $newbuilding->developer->id]];
$this->params['breadcrumbs'][] = ['label' => 'Жилые комплексы', 'url' => ['admin/newbuilding-complex/index',  'NewbuildingComplexSearch[developer_id]' => $newbuilding->developer->id]];
$this->params['breadcrumbs'][] = ['label' => $newbuilding->newbuildingComplex->name, 'url' => ['admin/newbuilding-complex/update', 'id' => $newbuilding->newbuildingComplex->id]];
$this->params['breadcrumbs'][] = ['label' => 'Позиции', 'url' => ['admin/newbuilding/index', 'newbuildingComplexId' => $newbuilding->newbuildingComplex->id]];
$this->params['breadcrumbs'][] = ['label' => $newbuilding->name, 'url' => ['admin/newbuilding/update', 'id' => $newbuilding->id]];
$this->params['breadcrumbs'][] = ['label' => 'Квартиры', 'url' => ['admin/flat/index', 'newbuildingId' => $newbuilding->id]];
$this->params['breadcrumbs'][] = ['label' => $flat->number, 'url' => ['admin/flat/update', 'id' => $flat->id]];
$this->params['breadcrumbs'][] = 'Обновить';
?>

<div class="flat-update white-block">
    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
        'newbuilding' => $newbuilding,
        'actions' => $actions,
        'flat' => $flat,
        'newbuilding' => $newbuilding,
        'backUrl' => ['admin/flat/index', 'newbuildingId' => $newbuilding->id],
    ]) ?>
</div>
