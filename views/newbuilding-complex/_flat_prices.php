<?php

use yii\helpers\Url;
use yii\helpers\Html;
use app\models\search\AdvancedFlatSearch;

$format = Yii::$app->formatter;
?>

<?php if(!is_null($model->getMinFlatPriceForRooms(1)) && !is_null($model->getMaxFlatPriceForRooms(1))): ?>
<a href="<?= Url::to([
                'site/search', 
                'AdvancedFlatSearch[roomsCount][0]' => 1,
                'AdvancedFlatSearch[flatType]' => AdvancedFlatSearch::FLAT_TYPE_STANDARD,
                'AdvancedFlatSearch[newbuilding_complex][]' => $model->id,
                'AdvancedFlatSearch[developer][]' => $model->developer->id,
            ]); ?>" class="link-list--item">
    <span>1- комнатные</span>
    <b>
        <?= $format->asCurrencyRange(round($model->getMinFlatPriceForRooms(1)), round($model->getMaxFlatPriceForRooms(1)), 'руб.'); ?>
    </b>
</a>
<?php endif; ?>

<?php if(!is_null($model->getMinFlatPriceForRooms(2)) && !is_null($model->getMaxFlatPriceForRooms(2))): ?>
<a href="<?= Url::to([
                'site/search', 
                'AdvancedFlatSearch[roomsCount][0]' => 2,
                'AdvancedFlatSearch[flatType]' => AdvancedFlatSearch::FLAT_TYPE_STANDARD,
                'AdvancedFlatSearch[newbuilding_complex][]' => $model->id,
                'AdvancedFlatSearch[developer][]' => $model->developer->id,
            ]); ?>" class="link-list--item">
    <span>2- комнатные</span>
    <b>
        <?= $format->asCurrencyRange(round($model->getMinFlatPriceForRooms(2)), round($model->getMaxFlatPriceForRooms(2)), 'руб.'); ?>
    </b>
</a>
<?php endif; ?>

<?php if(!is_null($model->getMinFlatPriceForRooms(3)) && !is_null($model->getMaxFlatPriceForRooms(3))): ?>
<a href="<?= Url::to([
                'site/search', 
                'AdvancedFlatSearch[roomsCount][0]' => 3,
                'AdvancedFlatSearch[flatType]' => AdvancedFlatSearch::FLAT_TYPE_STANDARD,
                'AdvancedFlatSearch[newbuilding_complex][]' => $model->id,
                'AdvancedFlatSearch[developer][]' => $model->developer->id,
            ]); ?>" class="link-list--item">
    <span>3- комнатные</span>
    <b>
        <?= $format->asCurrencyRange(round($model->getMinFlatPriceForRooms(3)), round($model->getMaxFlatPriceForRooms(3)), 'руб.'); ?>
    </b>
</a>
<?php endif; ?>

<?php if(!is_null($model->minStudioFlatPrice) && !is_null($model->maxStudioFlatPrice)): ?>
<a href="<?= Url::to([
                'site/search', 
                'AdvancedFlatSearch[flatType]' => AdvancedFlatSearch::FLAT_TYPE_STUDIO,
                'AdvancedFlatSearch[newbuilding_complex][]' => $model->id,
                'AdvancedFlatSearch[developer][]' =>$model->developer->id,
            ]); ?>" class="link-list--item">
    <span>Студии</span>
    <b>
        <?= $format->asCurrencyRange(round($model->minStudioFlatPrice), round($model->maxStudioFlatPrice), 'руб.'); ?>
    </b>
</a>
<?php endif; ?>