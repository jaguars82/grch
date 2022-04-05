<?php

use app\assets\ProfileAsset;
use yii\helpers\Html;

$imagePath = isset($path) ? $path : '';

ProfileAsset::register($this);
?>

<?php if(!\Yii::$app->user->isGuest): ?>
    <div class="white-bg">
        <div class="flex-row">
            <div class="person">
                <div class="image">
                    <?php if(!is_null($user->photo)): ?>
                    <?= Html::img(\Yii::getAlias("@web/uploads/{$user->photo}")) ?>
                    <?php else: ?>
                        <?= Html::img(\Yii::getAlias("@web/img/blank-person.png")); ?>
                    <?php endif; ?>
                </div>
                <div class="content">
                    <div class="flex-row">
                        <?php if(!is_null($user->roleLabel)): ?>
                        <b>
                            <?= $user->roleLabel ?>
                        </b>
                        <?php endif; ?>
                        <span class="name">
                            <?= $user->fullName ?>
                        </span>
                    </div>
                    <?php if(!is_null($user->phone)): ?>
                    <a href="tel:<?= $user->phone ?>" class="phone">
                        <span class="icon">
                            <?= Html::img(Yii::getAlias('@web/img/icons/phone.png'));?>
                        </span>
                        <?= $user->phone ?>
                    </a>
                    <?php endif; ?>
                    <?php if(!is_null($user->email)): ?>
                    <a href="mailto:<?= $user->email ?>" class="email">
                        <span class="icon">
                            <?= Html::img(Yii::getAlias('@web/img/icons/mail.png'));?>
                        </span>
                        <?= $user->email ?>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="agency-image">
                <?php if(
                    !is_null($user->agency)
                    && !is_null($user->agency->logo)
                ): ?>
                    <?= Html::img(["$imagePath/uploads/{$user->agency->logo}"]) ?>
                <?php else: ?>
                    <?= Html::img([Yii::getAlias("$imagePath/img/office.png")]) ?>
                <?php endif ?>
            </div>
        </div>
    </div>
<?php endif;?>