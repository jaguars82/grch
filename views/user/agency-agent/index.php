<?php
/* @var $this yii\web\View */

use yii\grid\GridView;
use app\assets\ProfileAsset;
use yii\helpers\Html;
use yii\widgets\Pjax;

$this->title = 'Агенты "'.$agency->name.'"';
$this->params['breadcrumbs'][] = ['label' => 'Кабинет пользователя', 'url' => ['user/profile']];
$this->params['breadcrumbs'][] = $this->title;

ProfileAsset::register($this);
?>

<div class="row">

    <div class="col-md-3">

    <?= $this->render('/user/_sideblock') ?>

    </div>

    <div class="col-md-9">
        <div class="white-block">
            <h2 class="bordered"><?= Html::encode($this->title) ?></h2>
            
            <div style="margin: 20px 0;" class="btn-group">
                <p><?= Html::a('Добавить агента', ['create', 'agencyId' => $agency->id], ['class' => 'btn btn-success', 'style' => 'margin-bottom: 10px']) ?></p>
            </div>

            <?php Pjax::begin([
                'id' => 'search-result',
                'enablePushState' => true,
                'enableReplaceState' => false,
            ]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'summary' => '',
                'emptyText' => 'Агенты ещё не добавлены',
                'columns' => [
                    [
                        'attribute' => 'fullName',
                        'value' => function ($agent) {
                            return Html::a($agent->fullName, ['user/agency-agent/update', 'agencyId' => $agent->agency_id, 'id' => $agent->id]);
                        },
                        'format' => 'raw',
                    ],
                    'email:email',
                    [
                        'attribute' => 'phone',
                        'value' => function ($agent) {
                            return !is_null($agent->phone) ? \Yii::$app->formatter->asPhoneLink($agent->phone) : null;
                        },
                        'format' => 'raw',
                    ],
                    'telegram_id',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'visibleButtons' => ['view' => false],
                        'urlCreator' => function ($action, $agent, $key, $index) {
                
                            if ($action === 'update') {
                                $url ='update?agencyId='.$agent->agency_id.'&id='.$agent->id;
                                return $url;
                            }
                            if ($action === 'delete') {
                                $url ='delete?agencyId='.$agent->agency_id.'&id='.$agent->id;
                                return $url;
                            }
                        }
                    ],
                ],
            ]); ?>

            <?php Pjax::end(); ?>
        </div>
    </div>

</div>