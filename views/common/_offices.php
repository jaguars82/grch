<?php
use yii\helpers\Html;

$format = \Yii::$app->formatter;
?>
<ul>
    <?php foreach($offices as $office): ?>
        <li>
            <?= $office->address ?>
        </li>
    <?php endforeach;?>
</ul>