<?php
use app\assets\widgets\ImageViewAsset;
use yii\helpers\Html;

ImageViewAsset::register($this);
?>

<div class="modal fade" id="photo-view" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="margin: 0 auto;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top:-17px;margin-right:-12px;">
            <span aria-hidden="true">&times;</span>
        </button>
        
        <div id="azimuth-block">
            <?= Html::img("/img/compass.svg", [ 'id' => 'azimuth', 'style' => 'z-index: 1; position:absolute; top: 5px; left: 5px; width: 100px']) ?>
        </div>
        
        <div class="text-center" style="overflow: hidden; height: 800px; vertical-align: middle; line-height: 800px">
            <?= Html::img("#", [ 'id' => 'photo-view-img', /*'width' => '85%'*/ 'style' => 'max-width:100%; max-height:100%;']) ?>
        </div>
      </div>
    </div>
  </div>
</div>



