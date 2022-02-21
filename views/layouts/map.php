<?php
/* @var $content string */
/* @var $this \yii\web\View */

use app\assets\NewAsset;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

NewAsset::register($this);
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
<div class="app">
    <?= $this->context->renderPartial('/common/_header.php') ?>
    <main>
        <?= $this->context->renderPartial('/common/_alert.php') ?>

        <?= $content ?>
    </main>
</div>
<div class="overlay"></div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>