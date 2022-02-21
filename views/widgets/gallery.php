<?php
use app\assets\widgets\GalleryAsset;
use yii\helpers\Html;
use yii\helpers\Url;

GalleryAsset::register($this);
?>
<?php if(count($images) > 0): ?>
<div class="swiper thumbs-full">
    <div class="swiper-wrapper">
        <?php foreach($images as $key => $image): ?>
        <div class="swiper-slide">
            <a href="<?= Url::to([\Yii::getAlias("@web/uploads/{$image->$fileField}")]) ?>" data-fancybox="gallery">
                <?= Html::img(\Yii::getAlias("@web/uploads/{$image->$fileField}")) ?>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<div class="swiper thumbs-gallery">
    <div class="swiper-wrapper">
        <?php foreach($images as $key => $image): ?>
        <div class="swiper-slide">
            <span style="background-image: url(<?= Url::to(\Yii::getAlias("@web/uploads/{$image->$fileField}")) ?>)"></span>
        </div>
        <?php endforeach; ?> 
    </div>
</div>
<?php endif; ?>