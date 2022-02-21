<?php
$format = \Yii::$app->formatter;
if (isset($discount)) {
    $discount *= 100;
}
?>

<span class="<?php if(isset($class)): ?><?= $class ?><?php else: ?>price-value<?php endif ?>">
    <?php if($condition): ?>
        <?= $format->asCurrencyRange($firstPrice, $secondPrice) ?>
        <?php if (isset($discount) && ($discount > 0)): ?>
            <br>
            <small>На данную квартиру действует скидка <b><?= $discount ?>%</b></small>
        <?php endif;?>
    <?php else: ?>
        <?php if(isset($onePrice)): ?>
            <?= $format->asCurrency($onePrice) ?>
        <?php elseif(isset($message)): ?>
            <?= $message ?>
        <?php endif ?>
    <?php endif ?>
</span>
