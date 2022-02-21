<?php 
use app\widgets\Alert; 
?>
<div class="container" style="position: absolute; padding: 0; margin-left: -15px; z-index: 2;">
    <div class="alert alert-template" role="alert" style="display: none; margin: 15px; margin-top: 0">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <div class="alert-content"></div>
    </div>
    <div class="alert-seat">
        <div id="alert-block" style="margin-left: 15px; margin-right: 15px">
            <?= Alert::widget() ?>
        </div>
    </div>
</div>