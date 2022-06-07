<?php
use app\assets\widgets\CompassRoseAsset;
use yii\helpers\Html;

CompassRoseAsset::register($this);
?>

<?php if($azimuth !== 0): ?>
<?= Html::img("/img/compass-rose-names.svg", [ 'id' => 'compass-rose-flat', 'data-azimuth' => $azimuth]) ?>
<?php endif; ?>


