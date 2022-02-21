<?php
use yii\helpers\Html;
use yii\helpers\Url;

$route = isset($route) ? $route : NULL;

$format = \Yii::$app->formatter;
?>

<div class="<?= $itemContactClass ?> item-contact" style="margin-bottom: 20px">
    <div class="row">
        <div class="col-md-3 text-center">
            <?php if(is_null($model->photo)): ?>
                <i class="glyphicon glyphicon-user" style="font-size: 50px; color: lightgray; display:block; margin-bottom: 5px"></i>
            <?php else: ?>
                <?= Html::img(["/uploads/{$model->photo}"], [ 'height' => 50]) ?>
            <?php endif ?>
            
            <div style="margin-top: 5px">
                <?php if(!empty($model->worktime)): ?>
                    <a href="#worktime" class="toggle_worktime" data-toggle="collapse" style="margin-right: 5px">
                        <i class="glyphicon glyphicon-time"></i>
                    </a>
                <?php endif ?>
                
                <?php if(!$noControls): ?>
                    <a href="<?= Url::to(["$route/update", 'id' => $model->id]) ?>" style="margin-right: 5px">
                        <i class="glyphicon glyphicon-pencil"></i>
                    </a>

                    <a 
                        href="javascript:void(0);"
                        class="delete-contact"
                        data-confirm1="Вы уверены, что хотите удалить контакт?"
                        data-method="post"
                        data-target="<?= Url::to(["$route/delete", 'id' => $model->id]) ?>"
                    >
                        <i class="glyphicon glyphicon-remove"></i>
                    </a>
                <?php endif ?>
            </div>
        </div>
        
        <div class="col-md-9">
            <h3 style="margin-top: 0; margin-bottom: 5px"><?= $model->type ?></h3>
            <div style="margin-top: 5px"><?= $model->person ?></div>
            <div style="margin-top: 5px"><?= $format->asPhoneLink($model->phone) ?></div>
        </div>
    </div>
    
    <?php if(!empty($model->worktime)): ?>
    <div class="collapse worktime-block" style="margin-top: 5px">
        <h4 style="font-size: 17px; margin-bottom: 5px">Время работы</h4>
        <?php foreach($format->asWorktimeArray($model->worktime) as $weekday => $times): ?>
        <div class="row">
            <div class="col-md-4"><b><?= $weekday ?></b></div>
            <div class="col-md-8"><?= $times['from'] ?>-<?= $times['to'] ?></div>
        </div>
        <?php endforeach ?>   
    </div>
    <?php endif ?>
</div>