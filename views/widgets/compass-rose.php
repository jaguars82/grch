<?php
use app\assets\widgets\CompassRoseAsset;
use yii\helpers\Html;

CompassRoseAsset::register($this);
?>

<?php if($azimuth !== 0): ?>
<?= Html::img("/img/compass-rose-names.svg", [ 'id' => $id, 'data-azimuth' => $azimuth]) ?>
<?php endif; ?>


