<?php

namespace app\models\form;

use app\components\traits\FillAttributes;
use app\components\traits\ProcessFile;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Form for bank's data
 */
class BankForm extends Model
{
    use FillAttributes;
    use ProcessFile;
    
    const SCENARIO_UPDATE = 'update';
    
    public $name;
    public $logo;
    public $address;
    public $longitude;
    public $latitude;
    public $email;
    public $phone;
    public $url;
    public $is_logo_reset = 0;
    
    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        $commonFields = ['name', 'logo', 'is_logo_reset', 'address', 'longitude', 'latitude', 'email', 'phone', 'url'];
        
        return [
            self::SCENARIO_DEFAULT => array_merge($commonFields, [/*'logo'*/]),
            self::SCENARIO_UPDATE => $commonFields,
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'logo'], 'required'],
            [['address', 'email', 'phone', 'url'], 'string'],
            [['name'], 'string', 'max' => 200],
            [['longitude', 'latitude'], 'double'],
            [['longitude', 'latitude'], 'default', 'value' => NULL],
            [['logo'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, gif, jpeg, svg'],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Название',
            'logo' => 'Логотип',
            'address' => 'Адрес',
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
