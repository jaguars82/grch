<?php
use yii\helpers\Html;

$format = \Yii::$app->formatter;
?>

<?php if(!is_null($model->address) || 
         !is_null($model->email) || 
         !is_null($model->url) || 
         !is_null($model->phone)
      ): ?>
<div class="contacts <?= $class?>">
    <?php if($model->address): ?>
    <span class="location">
        <?= $model->address ?>
    </span>
    <?php endif; ?>
    <?php if($model->email): ?>
        <?= Html::a($model->email, "mailto:$model->email", ['class' => 'mail']);?>
    <?php endif; ?>
    <?php if($model->url): ?>
        <?= Html::a($format->asHost($model->url), $model->url, ['class' => 'web', 'target' => '_blank']); ?>
    <?php endif; ?>
    <?php if($model->phone): ?>
        <span class="phone"><?= $model->phone ?></span>
    <?php endif; ?>
</div>
<?php endif; ?>