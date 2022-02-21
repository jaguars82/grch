<?php
use yii\helpers\Html;
?>

<div>
    <ul class="actions">
        <?php foreach($actions as $action): ?>
            <li><?= Html::a($action->actionData->resume, ['news/view', 'id' => $action->id]) ?></li>
        <?php endforeach ?>
    </ul>
</div>