<?php
/* @var $model app\models\Developer */

use app\models\News;
use yii\helpers\Html;
use yii\helpers\Url;

$format = \Yii::$app->formatter;
?>

<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-4 text-center">             
                <?php if(!is_null($model->image)): ?>
                    <?= Html::img([Yii::getAlias("@web/uploads/{$model->image}")], ['style' => 'max-width: 100%']) ?>
                <?php else: ?>
                    <?= Html::img([Yii::getAlias("@web/img/news.png")], ['height' => 200]) ?>
                <?php endif ?>
                
                <?php if(\Yii::$app->user->can('admin')): ?>
                <p style="margin-top: 20px">
                    <?= Html::a('Обновить', ['admin/news/update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('Удалить', ['admin/news/delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Вы уверены, что хотите удалить ' .(($model->category == News::CATEGORY_ACTION) ? 'акцию' : 'новость') . '?',
                            'method' => 'post',
                        ],
                    ]) ?>
                </p>
                <?php endif ?>
            </div>
            
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-6">
                        <?= News::$category[$model->category] ?>
                    </div>
                    <div class="col-md-6 text-right">
                        <?= $format->asDate($model->created_at, 'php:d.m.Y') ?>
                    </div>
                </div>

                <h2 style="margin-top: 10px"><?= $model->title?></h2>

                <?php if(count($model->newbuildingComplexes)): ?>
                <p>Застройщик: <?= Html::a($model->newbuildingComplexes[0]->developer->name, ['developer/view', 'id' => $model->newbuildingComplexes[0]->developer_id]) ?></p>
                <?php endif ?>
                
                <p class="text-justify" style="margin-top: 20px">
                    <?= $format->asHtml($model->detail) ?>
                </p>

                <div class="row" style="margin-top: 20px">
                    <div class="col-md-8">
                        <?php if(count($model->newbuildingComplexes)): ?>
                            Объекты, на которые распространяется:
                            <?php foreach($model->newbuildingComplexes as $newbuildingComplex): ?>
                            <div class="col-md-4 text-center" style="margin-top: 10px;">
                                <div style="height: 50px;">
                                    <a href="<?= Url::to(['newbuilding-complex/view', 'id' => $newbuildingComplex->id]) ?>" style="line-height: 40px">
                                        <?php if(!is_null($newbuildingComplex->logo)): ?>
                                            <?= Html::img([Yii::getAlias("@web/uploads/{$newbuildingComplex->logo}")], ['style' => 'max-height: 100%']) ?>
                                        <?php else: ?>
                                            <?= Html::img([Yii::getAlias("@web/img/newbuilding-complex.png")], ['height' => 50 ]) ?>
                                        <?php endif ?>
                                    </a>
                                </div>
                                <?= Html::a($newbuildingComplex->name, ['newbuilding-complex/view', 'id' => $newbuildingComplex->id]) ?>
                            </div>
                            <?php endforeach ?>
                        <?php endif ?>
                            
                        <?php if(!empty($model->search_link)): ?>
                        <div class="col-md-12" style="margin-top: 10px; padding: 0">
                            <?= Html::a('Перейти на страницу поиска', $model->search_link, ['class' => 'btn btn-primary']) ?>
                        </div>
                        <?php endif ?>
                    </div>
                    
                    <div class="col-md-4">
                        <?php if(count($model->newsFiles)): ?>
                            Документы:
                            <ul class="news-files">
                            <?php foreach($model->newsFiles as $newsFile): ?>
                                <li style="margin-bottom: 5px" style="">
                                    <a href="<?= Url::to(['news/download', 'id' => $newsFile->news_id, 'file' => $newsFile->name]) ?>">
                                        <i class="glyphicon glyphicon-file" style="font-size: 50px; color: lightgray; display:inline-block"></i>
                                    </a>
                                    <?= Html::a($newsFile->name, ['news/download', 'id' => $newsFile->news_id, 'file' => $newsFile->saved_name], ['style' => 'line-height: 50px; vertical-align: top;']) ?>
                                </li>
                            <?php endforeach ?>
                            </ul>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>