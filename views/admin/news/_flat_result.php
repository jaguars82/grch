<?php
use yii\widgets\ListView;
?>

<?php if (isset($errorText)): ?>
	<p><?= $errorText ?></p>
<?php else: ?>
    <div id="data-wrap">
        <?= ListView::widget([
            'dataProvider' => $dataProvider,
            'itemView' => '_flat_item',
            'layout' => "{sorter}\n{items}\n{pager}",
            'emptyText' => 'Подходящих квартир не нашлось. Попробуйте изменить параметры поиска.',
        ]); ?>
    </div>
    <?php if ($dataProvider->getTotalCount() > 0): ?>
        <input type="hidden" name="have_flats" value="1">
    <?php endif; ?>
    <!--<a class="btn btn-lg btn-primary" href="<?= $searchUrl ?>" target="_blank">Полный список квартир</a>-->
<?php endif; ?>