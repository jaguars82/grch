<?php
use yii\helpers\Url;
use yii\helpers\Html;

?>
<div class="header--location">
    <span class="header--location__selected">
        <?= $selectedCity->name ?>
    </span>
    
    <!-- the segment below is commented to temprory disable location selection -->
    <div class="header--location__dropdown">
        <div class="content">
            <?php foreach ($regions as $region): ?>
                <div id="region-<?= $region->id ?>-title" class="location-item_title" onclick="locationItemToggle('region', <?= $region->id ?>)">
                    <span class="text"><?= $region->name ?></span>
                    <span class="arrow material-icons-outlined" style="font-size: 20px; padding-top: 5px;">arrow_right</span>
                </div>
                <div id="region-<?= $region->id ?>-content" class="location-item_content">
                    <a class="location-item_title" href="<?= Url::current(['city' => $region->center->id]) ?>">
                        <span class="material-icons-outlined selectable-location-icon" style="font-size: 20px;">pin_drop</span>
                        <span class="selectable-location-name"><?= $region->center->name ?></span>
                    </a>
                    <?php foreach ($region->regionDistricts as $district): ?>
                        <?php if (isset($district->name)): ?>
                            <div id="district-<?= $district->id ?>-title" class="location-item_title" onclick="locationItemToggle('district', <?= $district->id ?>)">
                                <span><?= $district->name ?></span>
                                <span class="arrow material-icons-outlined" style="font-size: 20px; padding-top: 5px;">arrow_right</span>
                            </div>
                            <div id="district-<?= $district->id ?>-content" class="location-item_content">
                            <?php if (isset($district->center)): ?>
                                <a class="location-item_title" href="<?= Url::current(['city' => $district->center->id]) ?>">
                                    <span class="material-icons-outlined selectable-location-icon" style="font-size: 20px;">pin_drop</span>
                                    <span class="selectable-location-name"><?= $district->center->name ?></span>
                                </a>
                            <?php endif; ?>
                            <?php foreach ($district->cities as $location): ?>
                                <?php if ($location->is_district_center != 1): ?>
                                    <a class="location-item_title" href="<?= Url::current(['city' => $location->id]) ?>">
                                        <span class="material-icons-outlined selectable-location-icon" style="font-size: 20px;">pin_drop</span>
                                        <span class="selectable-location-name"><?= $location->name ?></span>
                                    </a>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>

            <!--
            <?php foreach($cities as $id => $name): ?>
                <?php if($id == $selectedCity->id): ?>
                    <?= Html::a($name, Url::current(['city' => $id]), [
                        'class' => 'selected'
                    ]); ?>
                <?php else: ?>
                    <?= Html::a($name, Url::current(['city' => $id])); ?>
                <?php endif; ?>
            <?php endforeach; ?>
            -->
        </div>
    </div>
</div>