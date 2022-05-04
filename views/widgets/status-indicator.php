<?php
use app\assets\widgets\StatusIndicatorAsset;
use yii\helpers\Html;

StatusIndicatorAsset::register($this);
?>

<?php if($status === true): ?>
<div>
    INDICATOR
    <?php if($amount > 0) ?>
    <span><?= $amount ?></span>
    <?php ?>
</div>
<?php endif; ?>