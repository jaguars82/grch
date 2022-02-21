<?php
use yii\helpers\Url;
use yii\helpers\Html;

?>
<div class="header--location">
    <span class="header--location__selected">
        Воронеж
    </span>
    
    <div class="header--location__dropdown">
        <div class="content">
            <?php foreach($cities as $id => $name): ?>
                <?= Html::a($name, Url::current(['city' => $id])); ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>