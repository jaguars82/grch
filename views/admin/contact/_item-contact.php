<?php
use yii\helpers\Html;

$format = \Yii::$app->formatter;
?>

<div class="row" style="margin-left: 0; margin-right: 0">
    <div class="col-md-2 text-center">
        <i class="glyphicon glyphicon-user contact-no-photo" style="font-size: 50px; color: lightgray; display:block; margin-bottom: 5px"></i>
        <?= Html::img(["#"], [ 'class' => 'contact-photo', 'height' => 50, 'style' => 'display: none']) ?>
    </div>

    <div class="col-md-10">
        <h3 style="margin-top: 0; margin-bottom: 5px" class="contact-type"></h3>
        <div style="margin-top: 5px" class="contact-person"></div>
        <div style="margin-top: 5px" class="contact-phone"><?= $format->asPhoneLink('#') ?></div>
    </div>
</div>