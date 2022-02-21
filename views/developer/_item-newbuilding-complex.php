<?php
/* @var $model app\models\Developer */

use yii\helpers\Html;
use yii\helpers\Url;

$format = \Yii::$app->formatter;
?>

<div class="col-md-12" style="padding-left: 0; padding-right: 0"">
    <div class="panel panel-default">
        <div class="panel-body" style="padding: 10px">
            <div class="row">
                <div class="col-md-2 text-center">
                    <a href="<?= Url::to(['newbuilding-complex/view', 'id' => $model->id]) ?>">
                        <?php if(!is_null($model->logo)): ?>
                            <?= Html::img([Yii::getAlias("@web/uploads/{$model->logo}")], ['style' => 'max-width: 100%; height: 80px']) ?>
                        <?php else: ?>
                            <?= Html::img([Yii::getAlias("@web/img/newbuilding-complex.png")], ['height' => 80]) ?>
                        <?php endif ?>
                    </a>
                </div>
                
                <div class="col-md-10">
                    <?= Html::a($model->name, ['newbuilding-complex/view', 'id' => $model->id]) ?><br>
                    
                    <?= $model->address ?><br>
                    
                    Комнаты: 
                    <?php if(count($model->rooms)): ?>
                        <?= implode(', ', $model->rooms) ?>
                    <?php else: ?>
                        данные отсутсвуют
                    <?php endif?>
                    <br>
                    
                    <div class="row">
                        <div class="col-md-6">
                            Стоимость:
                            <?php if(count($model->flats)): ?>
                                <?= $format->asCurrencyRange(floor($model->minFlatPrice), floor($model->maxFlatPrice)) ?>
                            <?php else: ?>
                                данные отсутсвуют
                            <?php endif?>
                        </div>
                        
                        <div class="col-md-6 text-right">
                            <span class="newbuilding-complex-item-link">
                                <?= Html::a('Описание ЖК', ['newbuilding-complex/view', 'id' => $model->id]) ?>
                            </span>
                            
                            <span class="newbuilding-complex-item-link">
                                <?= Html::a('Поиск по ЖК', [
                                    '/site/search', 
                                    'AdvancedFlatSearch[developer]' => $model->developer_id,
                                    'AdvancedFlatSearch[newbuilding_complex]' => $model->id,
                                ]) ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>