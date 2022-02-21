<?php
use yii\helpers\Html;

$this->title = 'Коммерческое предложение - ' . $model->flat->getAddress();

\app\assets\AppAsset::register($this);
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
    
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <?= $this->render('_common', [
                'flat' => $model->flat, 
                'settings' => json_decode($model->settings, true),
                'offer' => $model,
                'user' => $model->user,
                'isView' => true,
                'isPlacemarkImage' => true,
            ]) ?>
        </div>
    </div>
</div>
    
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>