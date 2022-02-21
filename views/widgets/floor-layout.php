<?php
use app\assets\widgets\FloorLayoutAsset;
use app\components\widgets\FloorLayout;
use yii\helpers\Html;

FloorLayoutAsset::register($this);
?>

<div class="modal fade" id="floor-layout-<?= $id ?>" tabindex="-1" role="dialog" style="margin: 0 auto;">
  <div class="modal-dialog" role="document" style="width: auto;">
    <div class="modal-content">
      <div class="modal-body">
        <button type="button" data-dismiss="modal" aria-label="Close" style="margin-top:-17px; margin-right:-12px; padding: 0; cursor: pointer; background: transparent; border: 0; float: right; font-size: 21px; font-weight: bold; line-height: 1; opacity: 0.2;">
            <span aria-hidden="true">&times;</span>
        </button>
        
        <div class="text-center" style="overflow: hidden">
            <?= Html::img("/uploads/{$image}", array_merge([
                'class' => 'floor-layout-img',
                'usemap' => "#flat_positions_{$id}",
            ], (in_array($mode, [FloorLayout::FLAT_SELECT_MODE, FloorLayout::FLOOR_VIEW_MODE])) 
                ? [] 
                : ['style' => 'width: 1000px'])
            ) ?>
            <map name="flat_positions_<?= $id ?>">
                <?php if ($mode === FloorLayout::FLAT_SELECT_MODE): ?>
                    <?php if(!is_null($map)): ?>
                        <?php foreach($map as $position => $data): ?>
                            <?php if (!is_null($flat->floor_position) && $position == $flat->floor_position): ?>
                                <area class="flat-position-area selected-area" shape="poly" data-position="<?= $position ?>" coords="<?= $data ?>" href="javascript:void(0);" data-maphilight='{"alwaysOn": true, "stroke": false, "fillColor": "<?= \app\components\widgets\FloorLayout::COLORS[$flat->status] ?>","fillOpacity": 0.4, "wrapClass": "floor-layout-view-wrap"}' title="Текущая позиция квартиры" data-color="<?= \app\components\widgets\FloorLayout::COLORS[$flat->status] ?>">
                            <?php elseif (in_array($position, $busyPositions)): ?>
                                <area class="flat-position-area-view" href="javascript:void(0)" shape="poly" coords="<?= $data ?>" href="javascript:void(0);" data-maphilight='{"alwaysOn": true, "stroke": false, "fillColor": "000000","fillOpacity": 0.4, "wrapClass": "floor-layout-view-wrap"}' title="Позиция уже занята">
                            <?php else: ?>
                                <area class="flat-position-area" data-position="<?= $position ?>" shape="poly" coords="<?= $data ?>" href="javascript:void(0);" data-maphilight='{"stroke": false, "fillColor": "<?= /*\app\components\widgets\FloorLayout::COLORS[$flat->status]*/"0000ff" ?>","fillOpacity": 0.4, "wrapClass": "floor-layout-view-wrap"}' title="Кликните чтобы выбрать позицию квартиры" data-color="<?= \app\components\widgets\FloorLayout::COLORS[$flat->status] ?>">
                            <?php endif ?>
                        <?php endforeach ?>
                    <?php endif ?>
                <?php elseif ($mode === FloorLayout::FLAT_VIEW_MODE): ?>
                    <!--area shape="poly" coords="?= $coords ?" href="javascript:void(0);" data-maphilight='{"alwaysOn": true, "stroke": false, "fillColor": "?= $color ?","fillOpacity": 0.4, "wrapClass": "floor-layout-view-wrap"}' title="?= $status ?"-->
                <?php elseif ($mode === FloorLayout::FLOOR_VIEW_MODE): ?>
                    <?php if(!is_null($map)): ?>
                        <?php foreach($map as $position => $data): ?>
                            <area class="flat-position-area-view" shape="poly" coords="<?= $data ?>" href="javascript:void(0);" data-maphilight='{"stroke": false, "fillColor": "0000ff","fillOpacity": 0.4, "wrapClass": "floor-layout-view-wrap"}' title="Позиция квартиры № <?= $position ?>">
                        <?php endforeach ?>
                    <?php endif ?>
                <?php endif ?>
            </map>
        </div>
      </div>
    </div>
  </div>
</div>



