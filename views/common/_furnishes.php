<?php

use yii\helpers\Json;
use yii\helpers\ArrayHelper;

$format = \Yii::$app->formatter;
?>

<?php if(!is_null($furnishes) && count($furnishes) > 0): ?>
    <?php 
    $count = 0; 
    foreach($furnishes as $furnish): 
        $images = ArrayHelper::getColumn($furnish->getFurnishImages()->asArray()->all(), 'image');
        foreach($images as $key => $image) {
            $images[$key] = \Yii::getAlias("@web/uploads/{$image}");
        }
    ?>
        <?php if(!is_null($images) && count($images) > 0): ?>
            <a href="javascript:void(0);" class="furnish-view" data-desc='<?= $format->asHtml($furnish->detail) ?>' data-images='<?= Json::encode($images) ?>'>
                <?= $furnish->name . (count($furnishes) > $count+1 ? ', ' : '') ?>
            </a>
        <?php else: ?>
            <?= $furnish->name . (count($furnishes) > $count+1 ? ', ' : '') ?>
        <?php endif; ?>
    <?php
    ++$count; 
    endforeach; 
    ?>
<?php else: ?>
    <?= $noDataMessage ?>
<?php endif; ?>