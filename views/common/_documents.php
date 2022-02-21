<?php

use yii\helpers\Html;
use yii\helpers\Url;

$format = \Yii::$app->formatter;
?>
<?php if(count($documents) > 0): ?>
    <?php foreach($documents as $document): ?>
        <a href="<?= Url::to(['newbuilding-complex/download-document', 'id' => $document->id])?>" class="document-list--item">
            <span class="title">
                <?= $format->asCapitalize($document->name) ?>. Добавлено <?= $format->asDate(strtotime($document->created_at), 'php:d.m.Y') ?>
            </span>
            <span class="info">
                <?= $document->file ?> - <?= $format->asFileSize($document->size) ?>
            </span>
        </a>
    <?php endforeach; ?>
<?php else: ?>
    <p>
        <?= $noDataMessage ?>
    </p>
<?php endif;?>