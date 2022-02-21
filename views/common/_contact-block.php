<?php

$format = \Yii::$app->formatter;
?>
<div class="<?= $class ?>">
    <?php if(!is_null($model->address)): ?>
    <span class="address">
        <?= $model->address ?>
    </span>
    <?php endif; ?>
    <?php if(!is_null($model->url)): ?>
        <a href="<?= $model->url ?>" class="web"><?= $format->asHost($model->url) ?></a>
    <?php endif; ?>
    <?php if(!is_null($model->email)): ?>
    <a href="mailto:<?= $model->email ?>" class="email"><?= $model->email ?></a>
    <?php endif; ?>
    <?php if(!is_null($model->phone)): ?>
    <a href="tel:<?= $model->phone ?>" class="phone"><?= $model->phone ?></a>
    <?php endif; ?>
</div>