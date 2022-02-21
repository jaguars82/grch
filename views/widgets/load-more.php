<?php
use app\assets\widgets\LoadMoreAsset;
use yii\helpers\Html;

LoadMoreAsset::register($this);
?>

<?php if ($totalCount > $pageSize): ?>                
<div class="text-center">                        
    <?= Html::a('Загрузить ещё', '#', [
        'class' => 'load-more-btn btn btn-primary',
        'data-page' => 1,
        'data-page-count'=> $pageCount,
        'data-container-id' => $containerId,
        'data-type' => $type,
    ])?>
    <p class="loading-img" style="display:none"><?= Html::img(["/img/loading.gif"], [ 'style' => 'height: 30px; margin-top: 10px']) ?></p>
</div>
<?php endif; ?>