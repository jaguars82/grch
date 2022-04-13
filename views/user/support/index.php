<?php

use app\assets\ProfileAsset;
use app\assets\SupportAsset;
use yii\helpers\Html;

$imagePath = isset($path) ? $path : '';

$this->title = 'Техподдержка';
$this->params['breadcrumbs'][] = ['label' => 'Кабинет пользователя', 'url' => ['user/profile/index']];
$this->params['breadcrumbs'][] = $this->title;

ProfileAsset::register($this);
SupportAsset::register($this);

?>

<?php if(!\Yii::$app->user->isGuest): ?>
    <div class="col-md-4 col-xl-3">

    <?= $this->render('/user/_sideblock', [
        'user' => $user
    ]) ?>

    </div>

    <div class="col-md-8 col-xl-9">
        <div class="white-block">
        </div>
    </div>
</div>
<?php endif; ?>