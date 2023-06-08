<?php

namespace app\models\form;

use app\components\traits\FillAttributes;
use app\components\traits\ProcessFile;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Form for agency's data
 */
class AgencyForm extends Model
{
    use FillAttributes;
    use ProcessFile;
    
    const SCENARIO_UPDATE = 'update';
    
    public $name;
    public $address;
    public $email;
    public $phone;
    public $url;
    public $longitude;
    public $latitude;
    public $logo;
    public $detail;
    public $offer_info;
    public $user_limit;
    
    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        $commonFields = ['name', 'address', 'email', 'phone', 'url', 'longitude', 'latitude', 'detail', 'offer_info', 'user_limit'];
        
        return [
            self::SCENARIO_DEFAULT => array_merge($commonFields, ['logo']),
            self::SCENARIO_UPDATE => $commonFields,
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'address', 'logo', 'detail'], 'required'],
            [['name', 'address'], 'string', 'max' => 200],
            [['longitude', 'latitude'], 'double'],
            [['user_limit'], 'integer'],
            [['detail', 'offer_info', 'email', 'phone', 'url'], 'string'],
            [['logo'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, gif, jpeg, svg'],
            [['longitude', 'latitude', 'user_limit', 'offer_info'], 'default', 'value' => NULL],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Название',
            'address' => 'Адрес',
            'logo' => 'Логотип',
            'detail' => 'Информация',
            'offer_info' => 'Информация для КП',
            'user_limit' => 'Лимит пользователей',
            'email' => 'Email',
            'phone' => 'Номер телефона',
            'url' => 'Сайт'
        ];
    }
    
    /**
     * Process request's data
     * 
     * @return boolean
     */
    public function process()
    {
        $this->logo = UploadedFile::getInstance($this, 'logo');
        
        if (!$this->validate()) {
            return false;
        }
        
        $this->processFile('logo');

        return true;
    }
}
