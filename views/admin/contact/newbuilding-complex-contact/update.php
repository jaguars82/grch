<?php
/* @var $model app\models\Contact */
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Обновить контакт: ' . $contact->person;
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Застройщики', 'url' => ['admin/developer/index']];
$this->params['breadcrumbs'][] = ['label' => $newbuildingComplex->developer->name, 'url' => ['admin/developer/update', 'id' => $newbuildingComplex->developer_id]];
$this->params['breadcrumbs'][] = ['label' => 'Жилые комплексы', 'url' => ['admin/newbuilding-complex/index',  'NewbuildingComplexSearch[developer_id]' => $newbuildingComplex->developer->id]];
$this->params['breadcrumbs'][] = ['label' => $newbuildingComplex->name, 'url' => ['admin/newbuilding-complex/update', 'id' => $newbuildingComplex->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="contact-update white-block">
    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

    <?= $this->render('/contact/_form', [
        'model' => $model,
        'ownerIdName' => 'newbuilding_complex_id',
        'backUrl' => Url::to(['index', 'newbuildingComplexId' => $newbuildingComplex->id]),
    ]) ?>
</div>
