<?php

namespace app\models;

use app\components\traits\FillAttributes;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use \yii\web\Cookie;

/**
 * User model
 *
 * @property integer $id
 * @property integer $agency_id
 * @property integer $developer_id
 * @property integer $status
 * @property string $first_name
 * @property string $last_name
 * @property string $middle_name
 * @property string $phone
 * @property string $email
 * @property string $password_hash
 * @property integer $passauth_enabled
 * @property string $telegram_id
 * @property string $otp
 * @property integer $otp_created_at
 * @property integer $otp_expired_at
 * @property string $auth_key
 * @property string $current_auth_token
 * @property string $telegram_chat_id
 * @property string $photo 
 * @property string $password_reset_token 
 * @property string $last_login 
 * @property string $last_ip 
 * @property integer $created_at
 * @property integer $updated_at
 */

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
            [['passauth_enabled'], 'integer'],
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

    public static function getDeveloperRepresentatives($developerId)
    {
        return self::find()
            ->where(['developer_id' => $developerId])
            ->all();
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
        $current_auth_token = Yii::$app->security->generateRandomString();

        $this->otp = null;
        $this->otp_created_at = null;
        $this->otp_expired_at = null;
        $this->last_login = date('Y-m-d H:i:s');
        $this->last_ip = Yii::$app->getRequest()->getUserIP();
        $this->current_auth_token = $current_auth_token;
        $this->save();

        $cookie = new Cookie([
            'name' => 'current_auth_token',
            'value' => $current_auth_token,
            // 'expire' => time() + 86400 * 365,
        ]);
        Yii::$app->getResponse()->getCookies()->add($cookie);

        // add authorization entry to visit_log table
        Yii::$app->visitLogger->logVisit(true);
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
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user (any) by email and agency
     *
     * @param string $email
     * @param integer $agencyId
     * @return static|null
     */
    public static function findByEmailAndAgency($email, $agencyId)
    {
        return static::findOne(['email' => trim($email), 'agency_id' => $agencyId]);
    }

    /**
     * Finds user (any) by phone and agency
     *
     * @param string $phone
     * @param integer $agencyId
     * @return static|null
     */
    public static function findByPhoneAndAgency($phone, $agencyId)
    {
        $phoneDigits = substr(preg_replace("/[^0-9]/", '', $phone), 1);
        return static::find()
            ->where(['like', 'phone', '%'.$phoneDigits.'%', false])
            ->andWhere(['=', 'agency_id', $agencyId])
            ->one();
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

        return $role === null ? null : $role->name;
    }

    public function getRoleLabel()
    {
        $roleLabels = [
            'admin' => 'Администратор',
            'manager' => 'Администратор агентства',
            'agent' => 'Агент',
            'developer_repres' => 'Представитель застройщика',
        ];

        return isset($this->role) ? $roleLabels[$this->role] : null;
    }


    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates otp
     *
     * @param string $otp password to validate
     * @return bool if otp provided is valid for current user
     */
    public function validateOtp($otp)
    {
        return $otp == $this->otp;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

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
        // return (is_null($this->otp) || (time() > strtotime('+1 minutes', $this->otp_created_at)) || (!is_null($this->otp) && !$this->isActualOtpCode()));
        return true;
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
