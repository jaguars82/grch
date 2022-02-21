<?php
use yii\helpers\Url;

$format = \Yii::$app->formatter;
?>

<tr>
    <td>
        <?= $model->name ?>
    </td>
    <td>
        <?= $format->asPercent($model->yearlyRateAsPercent) ?>
    </td>
    <td>
        <?= $format->asPercent($model->initial_fee_rate * 100) ?>
    </td>
    <td>
        <?= $model->payment_period ?> мес.
    </td>
</tr>