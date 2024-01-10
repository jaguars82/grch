<?php

namespace app\models\form;

use app\components\traits\FillAttributes;
use app\components\traits\ProcessFile;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Form for user's data
 */
class UserForm extends Model
{
    use FillAttributes;
    use ProcessFile;
    
    const SCENARIO_UPDATE = 'update';
    
    public $agency_id;
    public $first_name;
    public $last_name;
    public $middle_name;
    public $email;
    public $phone;    
    public $telegram_id;
    public $telegram_chat_id;
    public $photo;
    public $is_photo_reset = 0;
    
    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        $commonFields = ['first_name', 'last_name', 'middle_name', 'email', 'phone', 'telegram_id', 'telegram_chat_id', 'photo', 'is_photo_reset'];
        
        return [
            self::SCENARIO_DEFAULT => array_merge($commonFields, ['agency_id']),
            self::SCENARIO_UPDATE => $commonFields,
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name', 'email'], 'required'],
            [['first_name', 'last_name', 'middle_name'], 'string', 'max' => 100],
            [['phone', 'email', 'telegram_id', 'telegram_chat_id'], 'string', 'max' => 255],
            [['agency_id'], 'integer'],
            [['agency_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\Agency::className(), 'targetAttribute' => ['agency_id' => 'id']],
            [['agency_id', 'middle_name', 'phone', 'telegram_id', 'telegram_chat_id'], 'default', 'value' => NULL],
            [['photo'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, gif, jpeg, svg'],
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
            'photo' => 'Фото'
        ];
    }

    /**
     * Process request's data
     * 
     * @return boolean
     */
    public function process()
    {
        // $this->photo = UploadedFile::getInstance($this, 'photo');
        $this->photo = UploadedFile::getInstanceByName('photo');
        
        if (!$this->validate()) {
            return false;
        }
        
        $this->processFile('photo');

        return true;
    }
}
