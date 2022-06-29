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
            <span id="price-expand-button-<?= $model->id ?>" class="price-expand-button material-icons-outlined" onclick="handleDiscountsVisibility(<?= $model->id ?>)">chevron_right<span>
        </span>  
    <?php else: ?>
        <span class="price-value">
            <?= $format->asCurrency($model->price_cash); ?>
        </span>
    <?php endif; ?>
</div>

<?php if ($model->hasDiscount()): ?>
    <div id="discounts-container-<?= $model->id ?>" class="discounts-container">
        <div class="discount-item">
            <span class="discount-value"><?= $format->asCurrency($model->price_cash) ?></span>
            <span class="discount-badge">базовая цена</span>
        </div>
        <?php foreach ($model->allCashPricesWithDiscount as $actionDiscount): ?>
            <div class="discount-item">
                <span class="discount-value"><?= $format->asCurrency($actionDiscount['price']) ?></span>
                <?php if ($actionDiscount['discount_type'] == 0): ?>
                    <span class="discount-badge">- <?= $format->asPercent($actionDiscount['discount'] / 100) ?></span>
                <?php elseif ($actionDiscount['discount_type'] == 1): ?>
                    <span class="discount-badge">- <?= $format->asCurrency($actionDiscount['discount_amount']) ?></span>
                <?php elseif ($actionDiscount['discount_type'] == 2): ?>
                    <span class="discount-badge">цена по акции</span>
                <?php endif; ?>
                <?php if (!empty($actionDiscount['resume'])): ?>
                    <span class="discount-label">- <?= $actionDiscount['resume'] ?></span>
                <?php else: ?>
                    <span class="discount-label">- <?= $actionDiscount['title'] ?></span>
                <?php endif; ?>
                <span id="detail-<?= $actionDiscount['news_id'] ?>" class="discount-info-icon material-icons-outlined">info</span>
            </div>

            <!-- popup with action details -->
            <template id="action-detail-<?= $actionDiscount['news_id'] ?>" type="text/x-kendo-template">
                <div class="action-detail-container">
                    <?php if (!empty($actionDiscount['image'])): ?>
                    <div class="action-detail-img-container">
                        <img src="/uploads/<?= $actionDiscount['image'] ?>" />
                    </div>
                    <?php endif; ?>
                    <div class="action-detail-content-container">
                        <h4><?= $actionDiscount['title'] ?></h4>
                        <div><?= $actionDiscount['detail'] ?></div>
                    </div>
                    <div class="action-detail-button-container">
                        <a class="action-detail-linkbutton" target="_blank" href="/news/view?id=<?= $actionDiscount['news_id'] ?>">
                            <span>Подробнее</span>
                            <span class="material-icons-outlined">open_in_new</span>
                        </a>
                    </div>
                </div>
            </template>

        <?php endforeach; ?>
    </div>
<?php endif; ?>