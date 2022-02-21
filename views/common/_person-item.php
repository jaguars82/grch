<?php 
use yii\helpers\Html;
?>

<div class="<?= $itemClass ?> flex-row">
    <div class="info">
        <p class="name">
            <?= $model->fullName ?>
        </p>
        <?php if(!is_null($model->phone)): ?>
        <a href="tel:<?= $model->phone ?>" class="phone"><?= $model->phone ?></a>
        <?php endif; ?>
        <?php if(!is_null($model->email)): ?>
        <a href="mailto:<?= $model->email ?>" class="email"><?= $model->email ?></a>
        <?php endif; ?>
    </div>
    <div class="image">
        <?php if(!is_null($model->photo)): ?>
            <?= Html::img(\Yii::getAlias("@web/uploads/{$model->photo}")); ?>
        <?php else: ?>
            <?= Html::img(\Yii::getAlias("@web/img/blank-person.svg")); ?>
        <?php endif; ?>
    </div>
</div>