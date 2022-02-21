<?php

use yii\helpers\Html;
?>

<?php if(!is_null($floorLayouts) && count($floorLayouts) > 0): ?>
    <div class="newbuilding-card--layouts">
        <p class="h3 bordered">
            Планировки  этажей
        </p>
        <?php foreach($floorLayouts as $section => $items): ?>
            <div class="layouts-tab">
                <p class="layouts-tab--name">Подъезд <?= $section ?></p>
                <div class="layouts-tab--content">
                    <div class="row flex-row">
                        <?php foreach($items as $item): ?>
                            <div class="col-xs-6">
                                <p class="floor">Этаж <?= $item->floor?></p>
                                <div class="image">
                                    <?= Html::img(\Yii::getAlias("@web/uploads/{$item->image}")) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>