<?php

use app\assets\VirtualStructureAsset;

use app\models\Entrance;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

VirtualStructureAsset::register($this);

$virtualStructure = false;
$usedEntrancesIDs = [];

if (!empty($newbuildingComplex->virtual_structure)) {
    $virtualStructure = json_decode($newbuildingComplex->virtual_structure);
}

if ($virtualStructure !== false) {
    foreach ($virtualStructure as $position) {
        foreach ($position->entrances as $entrance) {
            array_push($usedEntrancesIDs, $entrance->id);
        }
    }
}

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
                    <?php if ($virtualStructure !== false): ?>
                        <?php foreach ($virtualStructure as $position): ?>
                            <div class="position-container" data-indexOfPosition="<?= $position->id ?>">
                                <div class="position-name-container">
                                    <input id="position-<?= $position->id ?>-name" class="position-name form-control" type="text" placeholder="Название позиции" onchange="processData()" value="<?= $position->name ?>">
                                </div>
                                <div class="entrances-container">
                                    <ul id="entrances-list-position-<?= $position->id ?>" class="position-entrances-list">
                                    <?php foreach ($position->entrances as $entrance): ?>
                                        <?php $dbEntrance = (new Entrance())->findOne($entrance->id); ?>
                                        <li id="entrance-edit-item-<?= $entrance->id ?>" class="entrance-edit-row" data-entranceid="<?= $entrance->id ?>"><span class="entrance-edit-row-marker material-icons-outlined">door_sliding</span><div class="inputs-field"><input type="hidden" name="entrance-id[]" value="<?= $entrance->id ?>"><input id="entrance-<?= $entrance->id ?>-name" type="hidden" name="entrance-name[]" value="<?= $dbEntrance->name ?> (<?= $dbEntrance->newbuilding->name ?>)"><input id="entrance-<?= $entrance->id ?>-alias" class="form-control" type="hidden" name="entrance-alias[]" placeholder="Псевдоним подъезда" onchange="processData()" value="<?= $entrance->alias ?>"></div><div class="entrance-draggable-item" id="<?= $entrance->id ?>"><?= $dbEntrance->name ?> (<?= $dbEntrance->newbuilding->name ?>)</div><span id="entrance-edit-item-remove-<?= $entrance->id ?>" onclick="removeEntranceItem(<?= $entrance->id ?>)" class="action-button material-icons-outlined">close</span></li>
                                    <?php endforeach; ?>
                                    </ul>
                                    <div id="accept-slot-<?= $position->id ?>" class="entrance-accept-slot" data-positionIndex="<?= $position->id ?>"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <button class="btn btn-primary btn-xs" onclick="addPosition()">Добавить позицию</button>
            
                <?php $form = ActiveForm::begin(['id' => 'virtual-structure-form', 'options' => ['enctype' => 'multipart/form-data']]); ?>
                <?= $form->field($newbuildingComplex, 'virtual_structure')->hiddenInput(['id' => 'virtual-structure-input'])->label(false) ?>
                <?= $form->field($newbuildingComplex, 'use_virtual_structure', [
                    'template' => "<label>{input}<span>Использовать виртуальную структуру</span></label>{error}",
                ])->checkbox([], false)->label(false) ?>
                <div class="form-group">
                    <?= Html::submitButton('Сохранить структуру', ['id' => 'virtual-structure-submit', 'class' => 'btn btn-success']) ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
            <div class="col-md-4">
                <div id="entrances-list">
                <?php foreach ($newbuildingComplex->entrances as $entrance): ?>
                    <?php if (!in_array($entrance->id, $usedEntrancesIDs)): ?>
                    <div class="entrance-draggable-item" id="<?= $entrance->id ?>"><?= $entrance->name ?> (<?= $entrance->newbuilding->name ?>)</div>
                    <?php endif; ?>
                <?php endforeach; ?>
                </div>
            </div>
        </div>

</div>