<?php
/* @var $model app\models\service\Entrance */
/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Добавить подъезд';

$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Застройщики', 'url' => ['admin/developer/index']];
$this->params['breadcrumbs'][] = ['label' => $newbuilding->newbuildingComplex->developer->name, 'url' => ['admin/developer/update', 'id' => $newbuilding->newbuildingComplex->developer->id]];
$this->params['breadcrumbs'][] = ['label' => 'Жилые комплексы', 'url' => ['admin/newbuilding-complex/index',  'NewbuildingComplexSearch[developer_id]' => $newbuilding->newbuildingComplex->developer->id]];
$this->params['breadcrumbs'][] = ['label' => $newbuilding->newbuildingComplex->name, 'url' => ['admin/newbuilding-complex/update', 'id' => $newbuilding->newbuildingComplex->id]];
$this->params['breadcrumbs'][] = ['label' => 'Позиции', 'url' => ['admin/newbuilding/index', 'newbuildingComplexId' => $newbuilding->newbuildingComplex->id]];
$this->params['breadcrumbs'][] = ['label' => 'Подъезды', 'url' => ['admin/entrance/index', 'newbuildingComplexId' => $newbuilding->id]];
$this->params['breadcrumbs'][] = 'Добавить';
?>

<div class="newbuilding-create white-block">
    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
        'backUrl' => ['admin/entrance/index', 'newbuildingId' => $newbuilding->id]
    ]) ?>
</div>
