<?php

namespace app\controllers\user;

use app\models\User;
use yii\web\Controller;

class ProfileController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index', [
            'user' => \Yii::$app->user->identity
        ]);
    }

}
