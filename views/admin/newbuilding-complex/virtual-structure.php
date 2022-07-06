<?php

use app\assets\VirtualStructureAsset;

use yii\helpers\Html;
use yii\widgets\ActiveForm;

VirtualStructureAsset::register($this);

$this->title = 'Виртуальная структура жилого комплекса ' . $newbuildingComplex->name;
$this->params['breadcrumbs'][] = ['label' => 'Администрированние', 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Застройщики', 'url' => ['admin/developer/index']];
$this->params['breadcrumbs'][] = ['label' => $newbuildingComplex->developer->name, 'url' => ['admin/developer/update', 'id' => $newbuildingComplex->developer->id]];
$this->params['breadcrumbs'][] = ['label' => 'Жилые комплексы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $newbuildingComplex->name, 'url' => ['update', 'id' => $newbuildingComplex->id]];
$this->params['breadcrumbs'][] = 'Виртуальная структура';

?>

<div class="newbuilding-complex-update white-block">
    
    <h2 class="bordered"><?= Html::encode($this->title) ?></h2>

        <div id="virtual-structure-form-container" class="row">
            <div class="col-md-8">
                <div id="positions-list">
                <!--<div class="position-container">
                    <div class="position-name-container">
                        <input class="form-control" type="text" placeholder="Название позиции">
                    </div>
                    <div class="entrances-container">
                        <ul class="position-entrances-list">
                        </ul>
                        <div class="entrance-accept-slot"></div>
                    </div>
                </div>-->
                </div>
                <button class="btn btn-primary btn-xs" onclick="addPosition()">Добавить позицию</button>
            <?php $form = ActiveForm::begin(['id' => 'virtual-structure-form', 'options' => ['enctype' => 'multipart/form-data']]); ?>
            <?php ActiveForm::end(); ?>

            </div>
            <div class="col-md-4">
                <div id="entrances-list">
                <?php foreach ($newbuildingComplex->entrances as $entrance): ?>
                    <div class="entrance-draggable-item" id="<?= $entrance->id ?>"><?= $entrance->name ?></div>
                <?php endforeach; ?>
                </div>
            </div>
        </div>

</div>