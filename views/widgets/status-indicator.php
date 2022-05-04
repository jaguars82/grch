<?php
use yii\widgets\Pjax;
use app\assets\widgets\StatusIndicatorAsset;
use yii\helpers\Html;

StatusIndicatorAsset::register($this);
?>

<?php Pjax::begin(['id' => $action.'Pjax', 'enablePushState' => false]); ?>

    <?= Html::beginForm([$url], 'post', ['data-pjax' => '', 'class' => 'form-inline']); ?>
    <?= Html::input('hidden', 'url', $url) ?>
    <?= Html::input('hidden', 'action', $action) ?>
    <!--<?= Html::input('hidden', 'watcher', \Yii::$app->user->id) ?>-->
    <?= Html::submitButton('обновить', ['class' => 'hidden', 'id' => $action.'Button', 'onclick' => 'event.stopPropagation();']) ?>
    <?= Html::endForm() ?>

    <?php if($status === true): ?>
    <div class="status-indicator text-center">
        <?php if($amount > 0) ?>
        <span class="status-indicator-amount"><?= $amount ?></span>
        <?php ?>
    </div>
    <?php endif; ?>

<?php Pjax::end(); ?>