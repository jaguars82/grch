<?php

namespace app\components;

use app\models\User;
use Yii;
use yii\base\ActionFilter;

class AuthAmountFilter extends ActionFilter
{
    public function beforeAction($action)
    {
        /**
         * Logout user (if he is a manager or an agent)
         * if he has authorized on another device (browser, etc.)
         */
        if(\Yii::$app->user->identity->role === 'agent' || \Yii::$app->user->identity->role === 'manager') {
            $cockie_current_auth_token = \Yii::$app->request->cookies->getValue('current_auth_token', null);
            $db_current_auth_token = \Yii::$app->user->identity->current_auth_token;

            if ($cockie_current_auth_token !== $db_current_auth_token) {
                \Yii::$app->user->logout();
            }
        }

        return true;
    }
}