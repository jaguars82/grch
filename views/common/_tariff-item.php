<?php
use yii\helpers\Url;
?>

<div class="item-tariff col-sm-6 col-lg-4" style="margin-bottom: 20px; text-align: center;">
    <h4><?= $model->name ?></h4>

    <?php if($model->yearlyRateAsPercent): ?>
        <p style="margin: 0 0 5px; white-space: nowrap;"><b>Процентная ставка годовых:</b> <?= $model->yearlyRateAsPercent ?>%</p>
    <?php endif; ?>

    <?php if($model->initial_fee_rate): ?>
        <p style="margin: 0 0 5px;"><b>Первоначальный взнос:</b> <?= $model->initial_fee_rate * 100 ?>%</p>
    <?php endif; ?>
    
    <?php if($model->payment_period): ?>
        <p style="margin: 0 0 15px;"><b>Срок ипотеки:</b> <?= $model->payment_period ?> мес.</p>
    <?php endif; ?>
</div>