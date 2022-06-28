<?php
use app\components\widgets\FloorLayout;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$format = \Yii::$app->formatter;
$id = "favorite-{$model->id}";
$updateCommentId = "update-comment-{$model->id}";
$commentForm = "favorite-comment-{$model->id}";
?>

<div class="flat-list--item">
    <div class="flat-list--item__image">
        <?php if(!is_null($model->flat->layout)): ?>
            <?= Html::img(["/uploads/{$model->flat->layout}"]) ?>
        <?php else: ?>
            <?= Html::img([Yii::getAlias("@web/img/flat.png")]) ?>
        <?php endif ?>
    </div>
    <div class="flat-list--item__content">
        <?= Html::a('', 
            'javascript:void(0);', [
            'class' => 'favorite in delete-favorite',
            'data' => [
                'target' => Url::to(['favorite/delete-flat', 'flatId' => $model->flat->id]),
                'method' => 'post',
            ],
        ]); ?>
        
        <?php if(!$model->isArchived()): ?>
            <?= Html::a('<i class="favorite-archive glyphicon glyphicon-time"></i>',
                //['favorite/archive', 'id' => $model->id],
                'javascript:void(0);',
                [
                    'class' => 'archive-favorite',
                    'data' => [
                        //'favorite' => $id,
                        'target' => Url::to(['favorite/archive', 'id' => $model->id]),
                        'method' => 'post',
                    ],
                ])
            ?>
        <?php else: ?>
            <?= Html::a('<i class="favorite-archive glyphicon glyphicon-repeat"></i>',
                //['favorite/activate', 'id' => $model->id],
                'javascript:void(0);',
                [
                    'class' => 'activate-favorite',
                    'title' => 'Помещен в архив: ' . $format->asDate($model->archived_by, 'php:d.m.Y'),
                    'data' => [
                        'target' => Url::to(['favorite/activate', 'id' => $model->id]),
                        'method' => 'post',
                    ],
                ])
            ?>
        <?php endif ?>

        <div class="flex-row info">
            <span class="price">
                <?php if($model->flat->hasDiscount()): ?>
                <?= $format->asCurrency($model->flat->cashPriceWithDiscount); ?>    
                <?php else: ?>
                <?= $format->asCurrency($model->flat->price_cash); ?>
                <?php endif; ?>
            </span>
            <span class="deadline">
                Сдача: <?= is_null($model->flat->newbuilding->deadline) ? 'Нет данных' : $format->asQuarterAndYearDate($model->flat->newbuilding->deadline, false) ?>
            </span>
        </div>
        <p class="area"><?= $format->asPricePerArea($model->flat->pricePerArea) ?></p>
        <p class="title">
            <?= Html::a($model->flat->newbuildingComplex->name, ['newbuilding-complex/view', 'id' => $model->flat->newbuildingComplex->id]) ?>
        </p>
        <div class="flex-row params">
            <span><?= $model->flat->roomsTitle ?></span>
            <span><?= $format->asArea($model->flat->area) ?></span>
            <span><?= $format->asFloor($model->flat->floor, $model->flat->newbuilding->total_floor) ?> этаж</span>
        </div>
        <div class="flex-row align-center developer-info">
            <div class="image">
                <?php if(!is_null($model->flat->newbuildingComplex->logo)): ?>
                    <?= Html::img([Yii::getAlias("@web/uploads/{$model->flat->newbuildingComplex->logo}")]) ?>
                <?php else: ?>
                    <?= Html::img([Yii::getAlias("@web/img/newbuilding-complex.png")]) ?>
                <?php endif ?>
            </div>
            <p class="address">
                <?= is_null($model->flat->newbuildingComplex->address) ? : $model->flat->newbuildingComplex->address ?>
            </p>
        </div>

        <?php $form = ActiveForm::begin([
            'action' => Url::to(['favorite/update-comment', 'id' => $model->id, 'type' => 'update']),
            'options' => [
                'class' => 'comment-form',
                'data' => ['favorite' => $id]
            ],
        ]); ?>
           <?= Html::textarea('comment', $model->comment, [
                'class' => 'form-control comment-field',
                'placeholder' => 'Комментарий'
            ]) ?>
        <?php ActiveForm::end(); ?>

        <?= Html::a('Подробнее', ['flat/view', 'id' => $model->flat->id], [
            'class' => 'btn btn-primary'
        ]) ?>

        <?php if($model->flat->hasDiscount()): ?>
            <div class="btn btn-red">
                Есть скидка
            </div>
        <?php endif; ?>
    </div>
</div>