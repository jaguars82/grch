<?php
use yii\helpers\Html;

$this->title = 'Коммерческое предложение';

$params = [
    'flat' => $flat, 
    'settings' => $settings,
    'path' => \Yii::getAlias('@webroot'),
    //'floorLayout' => $floorLayout,
    'user' => \Yii::$app->user->identity,
    'isPlacemarkImage' => true,
    'isView' => true,
];

/*if (!is_null($newPriceCash) && !empty($newPriceCash)) {
    $params['newPriceCash'] = $newPriceCash;
}

if (!is_null($newPriceCredit) && !empty($newPriceCredit)) {
    $params['newPriceCredit'] = $newPriceCredit;
}*/

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
    <!-- <title><?= Html::encode($this->title) ?></title> -->
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<?= $this->render('_pdf_body', $params) ?>
    
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>