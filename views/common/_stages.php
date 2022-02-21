<?php

use yii\helpers\Html;

$format = Yii::$app->formatter;
?>
<?php if(!is_null($stages) && count($stages) > 0): ?>
    <?php foreach($stages as $stage): ?>
        <div class="interactions-tab">
            <p class="interactions-tab--name">
                <?= $format->asCapitalize($stage->name); ?>
            </p>
            <div class="interactions-tab--content">
                <?= $format->asHtml($stage->description) ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>
        <?= $noDataMessage ?>
    </p>
<?php endif; ?>