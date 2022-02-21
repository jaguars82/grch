<?php
/* @var $model app\models\NewbuildingComplex */
/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = "Добавить жилой комплекс ({$developer->name})";
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Застройщики', 'url' => ['admin/developer/index']];
$this->params['breadcrumbs'][] = ['label' => $developer->name, 'url' => ['admin/developer/update', 'id' => $developer->id]];
$this->params['breadcrumbs'][] = ['label' => 'Жилые комплексы', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Добавить';
?>

<div class="newbuilding-complex-create white-block">
    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'newbuildingComplex' => $newbuildingComplex,
        'regions' => $regions,
        'cities' => $cities,
        'streetTypes' => $streetTypes,
        'buildingTypes' => $buildingTypes,
        'advantages' => $advantages,
        'savedImages' => [],
        'bankTariffs' => '',
        'backUrl' => ['admin/newbuilding-complex/index',  'NewbuildingComplexSearch[developer_id]' => $developer->id]
    ]) ?>
</div>
