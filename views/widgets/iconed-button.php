<?php
use app\assets\widgets\IconedButtonAsset;
use yii\helpers\Html;

IconedButtonAsset::register($this);
?>


<!--<?= Html::beginForm(['$action'], 'post', ['data-pjax' => '', 'class' => 'form-inline']); ?>-->
<!--<?= Html::input('hidden', 'url', '$url') ?>-->
<!--<?= Html::input('hidden', 'action', '$action') ?>-->
<!--<?= Html::input('hidden', 'watcher', \Yii::$app->user->id) ?>-->
<!--<?= Html::submitButton('обновить', ['class' => 'hidden', 'id' => 'Button', 'onclick' => 'event.stopPropagation();']) ?>-->
<!--<?= Html::endForm() ?>-->

<a class="iconed-button">
    <span class="<?= $icon_class ?>"><?= $icon ?></span>
</a>
