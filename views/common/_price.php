<?php
$format = \Yii::$app->formatter;
?>

<span class="<?php if(isset($class)): ?><?= $class ?><?php else: ?>price-value<?php endif ?>">
    <?php if($condition): ?>
        <?= $format->asCurrencyRange($firstPrice, $secondPrice) ?>
    <?php else: ?>
        <?php if(isset($onePrice)): ?>
            <?= $format->asCurrency($onePrice) ?>
        <?php elseif(isset($message)): ?>
            <?= $message ?>
        <?php endif ?>
    <?php endif ?>
</span>
