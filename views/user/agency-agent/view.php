<?php
/* @var $model app\models\User */
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->fullName;
$this->params['breadcrumbs'][] = ['label' => 'Агентства недвижимости', 'url' => ['agency/index']];
$this->params['breadcrumbs'][] = ['label' => $model->agency->name, 'url' => ['agency/view', 'id' => $model->agency->id]];
$this->params['breadcrumbs'][] = ['label' => 'Агенты', 'url' => ['agency/view', 'id' => $model->agency->id]];
$this->params['breadcrumbs'][] = $this->title;

\yii\web\YiiAsset::register($this);
?>

<div class="contact-view">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if(\Yii::$app->user->can('admin') || (\Yii::$app->user->can('manager') && $model->agency->hasCurrentUser())): ?>
    <p style="margin-bottom: 20px">
        <?= Html::a('Обновить', ['user/agency-agent/update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['user/agency-agent/delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить агента?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <?php endif ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label' => 'Агенство недвижимости',
                'value' => Html::a($model->agency->name, ['agency/view', 'id' => $model->agency->id]),
                'format' => 'raw',
            ],
            [
                'label' => 'Роль',
                'value' => 'Агент',
            ],
            'email:email',
            [
                'attribute' => 'phone',
                'value' => !is_null($model->phone) ? \Yii::$app->formatter->asPhoneLink($model->phone) : NULL,
                'format' => 'raw',
            ],
            'telegram_id',
            'telegram_chat_id',
        ],
    ]) ?>
</div>
