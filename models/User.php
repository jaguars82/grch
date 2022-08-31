<?php

namespace app\models;

use app\components\traits\FillAttributes;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    use FillAttributes;
    
    //public $email;

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    const OTP_LENGTH = 6;

    public static function tableName()
    {
        return 'user';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function rules()
    {
        return [
            [['first_name', 'last_name', 'email'], 'required'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            [['email', 'photo'], 'string'],
            [['phone', 'telegram_id', 'telegram_chat_id', 'agency_id', 'developer_id'], 'safe'],
            [['agency_id', 'developer_id', 'middle_name', 'phone', 'telegram_id', 'telegram_chat_id'], 'default', 'value' => NULL],
            [['photo'], 'unique'],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'middle_name' => 'Отчество',
            'email' => 'E-mail',
            'phone' => 'Телефон',
            'telegram_id' => 'Telegram ID',
            'telegram_chat_id' => 'Telegram Chat ID',
            'fullName' => 'ФИО',
            'photo' => 'Фото'
        ];
    }
        
    public function getFullName()
    {
        return "{$this->last_name} {$this->first_name} {$this->middle_name}";
    }
    
    /**
     * Gets query for [[Agency]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAgency()
    {
        return $this->hasOne(Agency::className(), ['id' => 'agency_id']);
    }

    /**
     * Gets query for [[Developer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDeveloper()
    {
        return $this->hasOne(Developer::className(), ['id' => 'developer_id']);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->auth_key = \Yii::$app->security->generateRandomString();
            }
            return true;
        }
        return false;
    }

    public function afterLogin($event)
    {
        $this->otp = null;
        $this->otp_created_at = null;
        $this->otp_expired_at = null;
        $this->save();
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findone(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
       return static::findOne(['access_token' => $token]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function getRole()
    {
        $roleList = Yii::$app->authManager->getRolesByUser($this->id);
        $role = array_shift($roleList);

        return $role->name;
    }

    public function getRoleLabel()
    {
        $roleLabels = [
            'admin' => 'Администратор',
            'manager' => 'Менеджер',
            'agent' => 'Агент',
            'developer_repres' => 'Представитель застройщика',
        ];

        return $roleLabels[$this->role];
    }


    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validateOtp($otp)
    {
        return $otp == $this->otp;
    }

    // public function setPassword($password)
    // {
    //     $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    // }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function sendOtpCode()
    {
        $code = $this->generateOtpCode();

        if (!$this->setOtpCode($code)) {
            return false;
        }

        return Yii::$app->mailer->compose()
            ->setTo($this->email)
            ->setFrom([\Yii::$app->params['senderEmail'] => \Yii::$app->params['senderName']])
            ->setSubject(Yii::t('app', 'Вход на сайт'))
            ->setTextBody("Ваш код для входа на сайт: $code")
            ->send();
    }

    // true если есть актуальный
    public function isActualOtpCode()
    {
        return (time() < $this->otp_expired_at);
    }

    public function canSendOtpCode()
    {
        return (is_null($this->otp) || (time() > strtotime('+1 minutes', $this->otp_created_at)) || (!is_null($this->otp) && !$this->isActualOtpCode()));
    }

    protected function setOtpCode($code)
    {
        $this->otp = $code;
        $this->otp_created_at = time();
        $this->otp_expired_at = strtotime('+5 minutes');

        if (!$this->validate()) {
            return false;
        }

        $this->save();

        return true;
    }

    protected function generateOtpCode()
    {
        $symbols = '0123456789';
        $result = '';

        for ($i = 1; $i <= self::OTP_LENGTH; $i++) {
            $result .= substr($symbols, (rand() % (strlen($symbols))), 1);
        }

        return $result;
    }
    
    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFavoriteFlats()
    {
        return $this->hasMany(Flat::className(), ['id' => 'flat_id'])
                ->via('favorites');
    }
    
    /**
     * Gets query for [[Favorite]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFavorites()
    {
        return $this->hasMany(Favorite::className(), ['user_id' => 'id']);
    }
}
