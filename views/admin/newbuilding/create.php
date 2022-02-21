<?php
/* @var $model app\models\Newbuilding */
/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Добавить этап';

$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Застройщики', 'url' => ['admin/developer/index']];
$this->params['breadcrumbs'][] = ['label' => $newbuildingComplex->developer->name, 'url' => ['admin/developer/update', 'id' => $newbuildingComplex->developer->id]];
$this->params['breadcrumbs'][] = ['label' => 'Жилые комплексы', 'url' => ['admin/newbuilding-complex/index',  'NewbuildingComplexSearch[developer_id]' => $newbuildingComplex->developer->id]];
$this->params['breadcrumbs'][] = ['label' => $newbuildingComplex->name, 'url' => ['admin/newbuilding-complex/update', 'id' => $newbuildingComplex->id]];
$this->params['breadcrumbs'][] = ['label' => 'Позиции', 'url' => ['admin/newbuilding/index', 'newbuildingComplexId' => $newbuildingComplex->id]];
$this->params['breadcrumbs'][] = 'Добавить';
?>

<div class="newbuilding-create white-block">
    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
        'regions' => $regions,
        'cities' => $cities,
        'streetTypes' => $streetTypes,
        'buildingTypes' => $buildingTypes,
        'advantages' => $advantages,
        'newbuildingComplex' => $newbuildingComplex,
        'backUrl' => ['admin/newbuilding/index', 'newbuildingComplexId' => $newbuildingComplex->id]
    ]) ?>
</div>
