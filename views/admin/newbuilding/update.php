<?php
/* @var $model app\models\Newbuilding */
/* @var $this yii\web\View */

use yii\helpers\Html;

$format = \Yii::$app->formatter;

$this->title = 'Обновить этап: ' . $format->asCapitalize($newbuilding->name);
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Застройщики', 'url' => ['admin/developer/index']];
$this->params['breadcrumbs'][] = ['label' => $newbuilding->developer->name, 'url' => ['admin/developer/update', 'id' => $newbuilding->developer->id]];
$this->params['breadcrumbs'][] = ['label' => 'Жилые комплексы', 'url' => ['admin/newbuilding-complex/index',  'NewbuildingComplexSearch[developer_id]' => $newbuildingComplex->developer->id]];
$this->params['breadcrumbs'][] = ['label' => $newbuilding->newbuildingComplex->name, 'url' => ['admin/newbuilding-complex/update', 'id' => $newbuilding->newbuildingComplex->id]];
$this->params['breadcrumbs'][] = ['label' => 'Позиции', 'url' => ['admin/newbuilding/index', 'newbuildingComplexId' => $newbuilding->newbuildingComplex->id]];
$this->params['breadcrumbs'][] = ['label' => $format->asCapitalize($newbuilding->name), 'url' => ['admin/newbuilding/update', 'id' => $newbuilding->id]];
$this->params['breadcrumbs'][] = 'Обновить';
?>

<div class="newbuilding-update white-block">
    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <div style="margin: 20px 0;">
        <?= Html::a('Планировки этажей', ['/admin/floor-layout/index', 'newbuildingId' => $newbuilding->id], ['class' => 'btn btn-primary']); ?>
        <?= Html::a('Подъезды', ['/admin/entrance/index', 'newbuildingId' => $newbuilding->id], ['class' => 'btn btn-primary']); ?>
        <?= Html::a('Квартиры', ['/admin/flat/index', 'newbuildingId' => $newbuilding->id], ['class' => 'btn btn-primary']); ?>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'regions' => $regions,
        'cities' => $cities,
        'streetTypes' => $streetTypes,
        'buildingTypes' => $buildingTypes,
        'advantages' => $advantages,
        'newbuildingComplex' => $newbuildingComplex,
        'backUrl' => ['admin/newbuilding/index', 'newbuildingComplexId' => $newbuilding->newbuildingComplex->id]
    ]) ?>
</div>
