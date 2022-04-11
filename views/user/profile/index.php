<?php

use app\assets\ProfileAsset;
use yii\helpers\Html;

$imagePath = isset($path) ? $path : '';

$this->title = 'Кабинет пользователя';
$this->params['breadcrumbs'][] = $this->title;

ProfileAsset::register($this);
?>


<?php if(!\Yii::$app->user->isGuest): ?>
<div class="row">
    <div class="col-md-4 col-xl-3">

    <?= $this->render('_sideblock', [
        'user' => $user
    ]) ?>

    </div>

    <div class="col-md-8 col-xl-9">
        <div class="card white-block">
            <div class="card-header">
                <div class="card-actions float-right">
                </div>
                <h5 class="card-title mb-0">Основные сведения</h5>
            </div>
            <div class="card-body h-100">

                <div class="media">
                    <div class="media-body">
                        <?php if(!is_null($user->phone)): ?>
                        <a href="tel:<?= $user->phone ?>" class="phone">
                            <span class="material-icons-outlined">phone</span>
                            <?= $user->phone ?>
                        </a>
                        <?php endif; ?>
                        <?php if(!is_null($user->email)): ?>
                        <a href="mailto:<?= $user->email ?>" class="email">
                            <span class="material-icons-outlined">email</span>
                            <?= $user->email ?>
                        </a>
                        <?php endif; ?>
                    </div>
                    <div>
                        <?= Html::a('Редактировать', ['user/profile/update', 'id' => $user->id], ['class' => 'btn btn-primary btn-sm']) ?>
                        <!--<a class="btn btn-primary btn-sm" href="#">Редактировать</a>-->
                    </div>
                </div>

                <hr>

            </div>
        </div>
    </div>
</div>
</div>
<?php endif; ?>