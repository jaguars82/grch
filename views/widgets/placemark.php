<?php
use app\assets\widgets\PlacemarkAsset;

PlacemarkAsset::register($this);
?>

<?php if(!is_null($longitude) && !is_null($latitude)): ?>
    <div class="address-block open">
        <div class="address-block--trigger flex-row">
            <span class="title"> На карте </span>
            <span class="text">
                <?= $address ?>
            </span>
        </div>
        <div style="display: block" class="address-block--map" id="map-container" data-longitude="<?= $longitude ?>" data-latitude="<?= $latitude ?>"></div>
    </div>
<?php endif ?>