<?php
$format = \Yii::$app->formatter;

$cashPrice = $flat->cashPriceWithDiscount;
$creditPrice = $flat->creditPriceWithDiscount;

if (!is_null($newPriceCash) && !empty($newPriceCash)) {
    $cashPrice= $newPriceCash;
}

if (!is_null($newPriceCredit) && !empty($newPriceCash)) {
    $creditPrice = $newPriceCredit;
}
?>

Адрес: <?= $flat->address ?>

<?php if(is_null($creditPrice) || empty($creditPrice) || $creditPrice === 0): ?>
Цена: <?= $format->asCurrency($cashPrice) ?>
<?php else: ?>
Цена(Нал.): <?= $format->asCurrency($cashPrice) ?>
    
Цена(Ипотека): <?= $format->asCurrency($creditPrice) ?>
<?php endif ?>

Площадь: <?= $format->asArea($flat->area) ?>