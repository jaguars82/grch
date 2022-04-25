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
<div class="row">

    <div class="col-md-3">

    <?= $this->render('/user/_sideblock') ?>

    </div>

    <div class="col-md-9">
        <div class="white-block">

        <?php if(Yii::$app->user->can('admin')): ?>
           ADMIN
        <?php else: ?>
            <?= Html::a('<div class="iconed-menu-item"><span class="material-icons-outlined">contact_support</span><span class="iconed-menu-label">Создать запрос</span></div>', ['user/support-ticket/create', 'id' => $user->id], ['class' => 'btn btn-primary btn-sm']) ?>
        <?php endif; ?>

        <?= $this->render('/user/support-ticket/index', [
            'tickets' => $tickets 
        ]) ?>

        </div>
    </div>

</div>
<?php endif; ?>