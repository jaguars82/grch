<?php
use app\assets\widgets\FlatPriceRangeAsset;

FlatPriceRangeAsset::register($this);

$format = \Yii::$app->formatter;
?>

<div class="price-container">
    <span class="price-label hidden">Стоимость</span>
    <?php if ($model->hasDiscount()): ?>
        <span class="price-value">
            <?= $format->asCurrencyRange($model->allCashPricesWithDiscount[0]['price'], $model->price_cash); ?>
            <span class="price-expand-button material-icons-outlined" onclick="handleDiscountsVisibility(<?= $model->id ?>)">chevron_right<span>
        </span>  
    <?php else: ?>
        <span class="price-value">
            <?= $format->asCurrency($model->price_cash); ?>
        </span>
    <?php endif; ?>
</div>

<?php if ($model->hasDiscount()): ?>
    <div id="discounts-container-<?= $model->id ?>" class="discounts-container">
        <div>
            <span class="discount-value"><?= $format->asCurrency($model->price_cash) ?></span>
            <span class="discount-label">- базовая цена</span>
        </div>
        <?php foreach ($model->allCashPricesWithDiscount as $actionDiscount): ?>
            <div>
                <span class="discount-value"><?= $format->asCurrency($actionDiscount['price']) ?></span>
                <span class="discount-label">- <?= $actionDiscount['id'] ?></span>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>