<?php
use yii\helpers\Url;
use yii\helpers\Html;

?>
<div class="header--location">
    <span class="header--location__selected">
        <?= $selectedCity->name ?>
    </span>
    
    <div class="header--location__dropdown">
        <div class="content">
            <?php foreach($cities as $id => $name): ?>
                <?php if($id == $selectedCity->id): ?>
                    <?= Html::a($name, Url::current(['city' => $id]), [
                        'class' => 'selected'
                    ]); ?>
                <?php else: ?>
                    <?= Html::a($name, Url::current(['city' => $id])); ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>