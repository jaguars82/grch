<?php
use app\components\widgets\FlatsChess;
use yii\helpers\Html;
use yii\helpers\Url;
$format = \Yii::$app->formatter;
?>
<tr> 
    <td>
        <?= $flatsArray[0]->floor ?>
    </td>
    <?php foreach(array_reverse($flatsArray) as $flatItem): ?>
        <td class="<?= FlatsChess::STATUS_CLASS[$flatItem->status] ?>
                   <?= !is_null($currentFlat) && $currentFlat->id == $flatItem->id ? 'current' : '' ?>
                   flat-item"
            data-flaturl="<?= Url::to(['flat/view', 'id' => $flatItem->id]) ?>">
            <div class="flex-row">
                <span class="rooms-count"><?= $flatItem->rooms ?></span>
                <span class="title"> Квартира </span>
                <span class="number">№ <?= $flatItem->number?></span>
            </div>
            <p class="price"><?= $format->asCurrency($flatItem->price_cash) ?></p>
            <div class="flex-row">
                <span class="area"> <?=  $format->asArea($flatItem->area) ?> </span>
                <span class="area-price"> - <?= $format->asPricePerArea($flatItem->pricePerArea) ?> </span>
            </div>
        </td>
    <?php endforeach; ?>

    <?php for($i = 0; $i < $maxRoomsOnFloor - count($flatsArray); $i++): ?>
        <td class="<?= FlatsChess::NO_FLAT_CLASS ?>"></td>
    <?php endfor ?>
</tr>