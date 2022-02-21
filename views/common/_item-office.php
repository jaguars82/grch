<?php
use yii\helpers\Url;

$route = isset($route) ? $route : NULL;

$format = \Yii::$app->formatter;
?>

<div class="item-office" style="margin-bottom: 20px">
    <h4><?= $model->name ?></h4>
    <p>Адрес: <br><?= $model->address ?></p>
    <?php if(!empty($model->phones)): ?>
        <p>Телефоны: </p>
        <div style="margin-bottom: 10px;" class="office-phones">
            <?php foreach($model->phones as $phone): ?>
                <div style="margin-bottom: 5px;">
                    <?= $phone['owner'] ?>
                </div>
                <div style="margin-bottom: 10px;">
                    <?= $format->asPhoneLink($phone['value']) ?>
                </div>
                <div class="clearfix"></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php if(!empty($model->comment)): ?>
        <p>
            <?= $model->comment ?>
        </p>
    <?php endif; ?>
    <?php if(!$noControls): ?>
        <a href="<?= Url::to(["$route/update", 'id' => $model->id]) ?>" style="margin-right: 5px">
            <i class="glyphicon glyphicon-pencil"></i>
        </a>

        <a 
            href="javascript:void(0);"
            class="delete-contact"
            data-confirm1="Вы уверены, что хотите удалить офис?"
            data-method="post"
            data-target="<?= Url::to(["$route/delete", 'id' => $model->id]) ?>"
        >
            <i class="glyphicon glyphicon-remove"></i>
        </a>
    <?php endif ?>
</div>