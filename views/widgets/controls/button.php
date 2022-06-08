<?php

use app\assets\widgets\controls\ButtonAsset;

ButtonAsset::register($this);

?>

<div class="c-button-wrapper <?= $class ?>" <?php if (!empty($wrapper_style)): ?>style="<?= $wrapper_style ?>"<?php endif; ?>>
    <span id="<?= $button_id ?>" class="c-button material-icons-outlined"><?= $icon ?></span>
</div>