<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Document */

$this->title = 'Обновить документ: ' . $document->name;

$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Застройщики', 'url' => ['admin/developer/index']];
$this->params['breadcrumbs'][] = ['label' => $newbuildingComplex->developer->name, 'url' => ['admin/developer/update', 'id' => $newbuildingComplex->developer->id]];
$this->params['breadcrumbs'][] = ['label' => 'Жилые комплексы', 'url' => ['admin/newbuilding-complex/index']];
$this->params['breadcrumbs'][] = ['label' => $newbuildingComplex->name, 'url' => ['admin/newbuilding-complex/update', 'id' => $newbuildingComplex->id]];
$this->params['breadcrumbs'][] = ['label' => 'Документы', 'url' => ['index', 'newbuildingComplexId' => $newbuildingComplex->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-update white-block">

    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
        'document' => $document
    ]) ?>

</div>
