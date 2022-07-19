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
                <div class="location-item_container">
                    <span><?= $region->name ?></span>
                    <span class="material-icons-outlined">arrow_right</span>
                </div>
                <?= $region->center->name ?>
                <?php foreach ($region->regionDistricts as $district): ?>
                    <?php if (isset($district->name)): ?>
                        <div class="location-item_container">
                            <span><?= $district->name ?></span>
                            <span class="material-icons-outlined">arrow_right</span>
                        </div>
                        <?php if (isset($district->center)): ?>
                            <div><?= $district->center->name ?></div>
                        <?php endif; ?>
                        <?php foreach ($district->cities as $location): ?>
                            <?php if ($location->is_district_center != 1): ?>
                                <div><?= $location->name ?></div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
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