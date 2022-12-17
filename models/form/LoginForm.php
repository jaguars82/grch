<?php

namespace app\models\form;

use app\models\User;
use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 */
class LoginForm extends Model
{
    public $loginway;
    public $email;
    public $password;
    public $otp;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['email', 'password', 'otp'], 'required'],
            [['loginway'], 'string'],
            ['otp', 'validateOtp'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the otp.
     * This method serves as the inline validation for otp.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateOtp($attribute, $param)
    {
        if ($this->loginway === 'pass') return true; // do not validate otp when login via password

        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validateOtp($this->otp)) {
                \Yii::$app->session->setFlash('error', 'Неправильный код. Попробуйте отправить новый код на e-mail через некоторое время');
                $this->addError($attribute, 'Incorrect code.');
            }
        }
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $param)
    {
        if ($this->loginway === 'otp') return true; // do not validate password when login via otp

        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user->passauth_enabled) {
                \Yii::$app->session->setFlash('error', 'Вход по паролю не настроен. Пожалуйста, авторизуйтесь по коду на email и создайте пароль в Личном Кабинете');
                $this->addError($attribute, 'Password not set.');
                return;
            }

            if (!$user || !$user->validatePassword($this->password)) {
                \Yii::$app->session->setFlash('error', 'Неверный пароль или адрес электронной почты');
                $this->addError($attribute, 'Incorrect email or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided email and password.
     * @return bool whether the user is logged in successfully
     */
    public function passlogin()
    {
        //if ($this->validatePassword()) {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(),  3600 * 8);
        }
        return false;
    }

    /**
     * Logs in a user using the provided email and otp.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        //if ($this->validateOtp()) {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(),  3600 * 8);
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByEmail($this->email);
        }

        return $this->_user;
    }
}
