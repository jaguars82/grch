<?php

namespace app\models\form;

use app\components\traits\FillAttributes;
use app\components\traits\ProcessFile;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Form for developer's data
 */
class DeveloperForm extends Model
{
    use FillAttributes;
    use ProcessFile;
    
    const SCENARIO_UPDATE = 'update';
    
    public $name;
    public $address;
    public $longitude;
    public $latitude;
    public $logo;
    public $detail;
    public $offer_info;
    public $free_reservation;
    public $paid_reservation;
    public $sort;
    public $phone;
    public $url;
    public $email;
    
    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        $commonFields = [
            'name', 'address', 'longitude', 'latitude', 'detail', 'offer_info',
            'free_reservation', 'paid_reservation', 'sort', 'phone', 'url', 'email'
        ];
        
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
            [['name', 'address', 'logo', 'detail', 'sort'], 'required'],
            [['name', 'address'], 'string', 'max' => 200],
            [['phone', 'url', 'email'], 'string'],
            [['longitude', 'latitude'], 'double'],
            [['detail', 'free_reservation', 'paid_reservation', 'offer_info'], 'string'],
            [['logo'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, gif, jpeg, svg'],
            [['longitude', 'latitude', 'free_reservation', 'paid_reservation', 'offer_info'], 'default', 'value' => NULL],
            [['sort'], 'filter', 'filter' => function ($value) {
                return intval(preg_replace('/[^0-9]/', '', $value));
            }],
            [['sort'], 'integer'],
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
            'free_reservation' => 'Условия бесплатной брони',
            'paid_reservation' => 'Условия платной брони',
            'sort' => 'Сортировка',
            'phone' => 'Номер телефона',
            'url' => 'Адрес сайта',
            'email' => 'Email'
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
