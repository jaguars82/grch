<?php
/* @var $model app\models\Newbuilding */
/* @var $this yii\web\View */

use yii\helpers\Html;

$format = \Yii::$app->formatter;

$this->title = 'Обновить ' . $entrance->name;
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Застройщики', 'url' => ['admin/developer/index']];
$this->params['breadcrumbs'][] = ['label' => $entrance->newbuilding->developer->name, 'url' => ['admin/developer/update', 'id' => $entrance->newbuilding->developer->id]];
$this->params['breadcrumbs'][] = ['label' => 'Жилые комплексы', 'url' => ['admin/newbuilding-complex/index',  'NewbuildingComplexSearch[developer_id]' => $entrance->newbuilding->newbuildingComplex->developer->id]];
$this->params['breadcrumbs'][] = ['label' => $entrance->newbuilding->newbuildingComplex->name, 'url' => ['admin/newbuilding-complex/update', 'id' => $entrance->newbuilding->newbuildingComplex->id]];
$this->params['breadcrumbs'][] = ['label' => 'Позиции', 'url' => ['admin/newbuilding/index', 'newbuildingComplexId' => $entrance->newbuilding->newbuildingComplex->id]];
$this->params['breadcrumbs'][] = ['label' => $format->asCapitalize($entrance->newbuilding->name), 'url' => ['admin/newbuilding/update', 'id' => $entrance->newbuilding->id]];
$this->params['breadcrumbs'][] = ['label' => $format->asCapitalize($entrance->name), 'url' => ['admin/entrance/update', 'id' => $entrance->id]];
$this->params['breadcrumbs'][] = 'Обновить';
?>

<div class="entrance-update white-block">
    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <div style="margin: 20px 0;">
        <?= Html::a('Квартиры', ['/admin/flat/index', 'entranceId' => $entrance->id], ['class' => 'btn btn-primary']); ?>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'backUrl' => ['admin/entrance/index', 'newbuildingId' => $entrance->newbuilding->id]
    ]) ?>
</div>