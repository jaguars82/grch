<?php

use app\assets\VirtualStructureAsset;

use yii\helpers\Html;

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

        <div id="virtual-structure-form" class="row">
            <div class="col-md-8">
                <div class="entrances-slot" style="height: 50px; border: solid thin #000; margin: 5px;"></div>
                <div class="entrances-slot" style="height: 50px; border: solid thin #000; margin: 5px;"></div>
            </div>
            <div class="col-md-4">
                <ul id="entrances-list">
                <?php foreach ($newbuildingComplex->entrances as $entrance): ?>
                    <li id="<?= $entrance->id ?>"><?= $entrance->name ?></li>
                <?php endforeach; ?>
                </ul>
            </div>
        </div>

</div>