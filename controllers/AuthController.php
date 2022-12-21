<?php

namespace app\controllers;

use app\models\User;
use app\models\form\LoginForm;
use Yii;
use yii\web\Controller;

class AuthController extends Controller
{
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post())) {

            //echo '<pre>'; var_dump($model); echo '</pre>'; die;
            $postData = Yii::$app->request->post();
            
            switch($postData['LoginForm']['loginway']) {
                case 'otp':
                    $login = $model->login();
                    $defaultLogin = 'otp';
                    break;
                case 'pass':
                    $login = $model->passlogin();
                    $defaultLogin = 'pass';
                    break;
                default:
                    $login = false;
            }

            if ($login) {
                if (parse_url(Yii::$app->getUser()->getReturnUrl(), PHP_URL_PATH) === '/') {
                    return $this->redirect('/' . ((\Yii::$app->request->cookies->has('site-index-query-string-' . \Yii::$app->user->id)) ? '?' . \Yii::$app->request->cookies->get('site-index-query-string-' . \Yii::$app->user->id) : ''));
                } else {
                    //return $this->goBack();
                    return $this->redirect(['login',
                        'model' => $model,
                        'defaultLogin' => isset($defaultLogin) ? $defaultLogin : 'otp',
                    ]);
                }
            }
        }

        $model->otp = '';
        
        return $this->render('login', [
            'model' => $model,
            'defaultLogin' => isset($defaultLogin) ? $defaultLogin : 'otp',
        ]);
    }


    public function actionSendCode()
    {
        $email = Yii::$app->request->post('email');
        if (is_null($email)) {
            return false;
        }

        $user = User::findByEmail($email);
        
        if (is_null($user)) {
            return json_encode(['error' => 'bad_email']);
        }
        
        if (!$user->canSendOtpCode()) {
            return json_encode(['error' => 'cant_send_code']);
        }

        $user->sendOtpCode();

        return json_encode(array('status' => true));
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

}
