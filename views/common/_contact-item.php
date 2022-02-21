<?php 
use yii\helpers\Html;

$format = \Yii::$app->formatter;
?>

<div class="contacts-list--item">
    <p class="title">
        <?= $format->asCapitalize($model->type) ?>
    </p>
    <div class="content">
        <div class="person-item flex-row">
            <div class="info">
                <p class="name">
                    <?= $model->person ?>
                </p>
                <?php if(!is_null($model->phone)): ?>
                    <a href="tel:<?= $model->phone ?>" class="phone"><?= $model->phone ?></a>
                <?php endif; ?>
                <?php if(!empty($model->worktime)): ?>
                    <div style="width: 100%;">
                        <a href="#worktime" class="worktime-label" data-toggle="collapse" style="margin-right: 5px">
                            Время работы
                        </a>
                        <div class="collapse worktime-block" style="margin-top: 5px">
                            <?php foreach($format->asWorktimeArray($model->worktime) as $weekday => $times): ?>
                                <div class="row">
                                    <div class="col-md-8"><b><?= $weekday ?></b></div>
                                    <div class="col-md-4"><?= $times['from'] ?>-<?= $times['to'] ?></div>
                                </div>
                            <?php endforeach ?>
                        </div>
                    </div>
                <?php endif ?>
            </div>
            <div class="image">
                <?php if(!is_null($model->photo)): ?>
                    <?= Html::img(\Yii::getAlias("@web/uploads/{$model->photo}")); ?>
                <?php else: ?>
                    <?= Html::img(\Yii::getAlias("@web/img/blank-person.svg")); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>