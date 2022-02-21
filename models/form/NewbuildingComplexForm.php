<?php

namespace app\models\form;

use app\components\traits\FillAttributes;
use app\components\traits\ProcessFile;
use yii\base\Model;
use yii\web\UploadedFile;
use app\models\City;
use app\models\District;

/**
 * Form for newbuilding complex's data
 */
class NewbuildingComplexForm extends Model
{
    use FillAttributes;
    use ProcessFile;
    
    const SCENARIO_UPDATE = 'update';
    
    public $developer_id;
    public $name;
    public $active;
    public $region_id;
    public $city_id;
    public $district_id;
    public $street_type_id;
    public $street_name;
    public $building_type_id;
    public $building_number;
    public $longitude;
    public $latitude;
    public $logo;
    public $is_logo_reset = 0;
    public $is_master_plan_reset = 0;
    public $detail;
    public $offer_info;
    public $offer_new_price_permit;
    public $algorithm;
    public $project_declaration;
    public $banks = [];
    public $savedBanks = [];
    public $advantages = [];
    public $images = [];
    public $savedImages = [];
    public $remove_project_declaration = 0;
    public $master_plan;
    public $stages = [];
    public $bank_tariffs = [];

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        $commonFields = [
            'name', 'active', 'detail', 'offer_info', 'advantages',
            'offer_new_price_permit', 'algorithm', 'banks',
            'longitude', 'latitude', 'offer_info', 'remove_project_declaration',
            'logo', 'is_logo_reset', 'is_master_plan_reset', 'region_id', 'city_id', 'district_id', 'street_type_id', 'street_name', 
            'building_number', 'master_plan', 'images', 'savedImages', 'stages', 'bank_tariffs'
        ];
        
        return [
            self::SCENARIO_DEFAULT => array_merge($commonFields, ['developer_id']),
            self::SCENARIO_UPDATE => array_merge($commonFields, ['remove_project_declaration']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['developer_id', 'name', 'offer_new_price_permit'], 'required'],
            [['logo', 'master_plan'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, gif, jpeg, svg'],
            [['images'],  'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, gif, jpeg, svg', 'maxFiles' => 100],
            [['longitude', 'latitude'], 'double'],
            [['building_number'], 'string', 'max' => 20],
            [['building_type_id', 'street_type_id', 'district_id', 'city_id', 'region_id', 'developer_id', 'offer_new_price_permit'], 'integer'],
            [['name', 'street_name'], 'string', 'max' => 200],
            [['detail', 'algorithm', 'offer_info'], 'string'],
            [['building_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\BuildingType::className(), 'targetAttribute' => ['building_type_id' => 'id']],
            [['street_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\StreetType::className(), 'targetAttribute' => ['street_type_id' => 'id']],
            [['district_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\District::className(), 'targetAttribute' => ['district_id' => 'id']],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\Region::className(), 'targetAttribute' => ['region_id' => 'id']],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\City::className(), 'targetAttribute' => ['city_id' => 'id']],
            [['developer_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\Developer::className(), 'targetAttribute' => ['developer_id' => 'id']],
            [['project_declaration', 'remove_project_declaration', 'banks', 'advantages', 'stages', 'bank_tariffs'], 'safe'],
            [['bank_tariffs'], 'validateBankTariffs', 'skipOnEmpty' => false, 'skipOnError' => false],
            [['longitude', 'latitude', 'detail', 'algorithm', 'offer_info'], 'default', 'value' => NULL],
            [['active'], 'boolean'],
            [['active'], 'default', 'value' => 1]
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Название',
            'active' => 'Активность',
            'street_name' => 'Название улицы',
            'building_number' => 'Номер здания',
            'longitude' => 'Долгота',
            'latitude' => 'Широта',
            'logo' => 'Логотип',
            'detail' => 'Информация',
            'offer_info' => 'Информация для КП',
            'offer_new_price_permit' => 'Возможность изменить цену КП',
            'algorithm' => 'Алгоритм действия',
            'project_declaration' => 'Проектная декларация',
            'master_plan' => 'Генплан',
            'images' => 'Фото ЖК',
            'stages' => 'Этапы взаимодействия',
        ];
    }

    public function validateBankTariffs($attribute) 
    {
        if(!$this->banks) {
            return;
        }
        
        if(!empty($this->bank_tariffs) && is_array($this->bank_tariffs)) {
            foreach($this->bank_tariffs as $bankId => $tariffs) {
                if(!in_array($bankId, $this->banks)) {
                    unset($this->bank_tariffs[$bankId]);
                    continue;
                }
                foreach($tariffs as $key => $tariff) {
                    $fieldPrefix = "{$attribute}[{$bankId}][{$key}]";

                    if(!isset($tariff['name']) || empty($tariff['name'])) {
                        $this->addError($fieldPrefix . '[name]', 'Выберите название тарифа');
                    }
                    
                    if(!isset($tariff['yearlyRateAsPercent']) || empty($tariff['yearlyRateAsPercent'])) {
                        $this->addError($fieldPrefix . '[yearlyRateAsPercent]', "Заполните поле 'Процентная ставка годовых'");
                    } elseif(!is_numeric($tariff['yearlyRateAsPercent'])) {
                        $this->addError($fieldPrefix. '[yearlyRateAsPercent]', "Поле 'Процентная ставка годовых' должно быть числом");
                    }
                    
                    if(!isset($tariff['initialFeeRateAsPercent']) || empty($tariff['initialFeeRateAsPercent'])) {
                        $this->addError($fieldPrefix . '[initialFeeRateAsPercent]', "Заполните поле 'Первоначальный взнос'");
                    } elseif(!is_numeric($tariff['initialFeeRateAsPercent'])) {
                        $this->addError($fieldPrefix . '[initialFeeRateAsPercent]', "Поле 'Первоначальный взнос' должно быть числом");
                    }

                    if(!isset($tariff['payment_period']) || empty($tariff['payment_period'])) {
                        $this->addError($fieldPrefix . '[payment_period]', "Заполните поле 'Срок ипотеки'");
                    } elseif(!is_numeric($tariff['payment_period'])) {
                        $this->addError($fieldPrefix . '[payment_period]', "Поле 'Срок ипотеки' должно быть числом");
                    }
                }
            }
        }
    }
    
    public function beforeValidate()
    {   
        if((!is_null($this->region_id) && !empty($this->region_id))
            && (!is_null($this->city_id) && !empty($this->city_id))) {
            $city = City::findOne($this->city_id);
            if(is_null($city) || empty($city) || $city->region->id != $this->region_id) {
                $this->city_id = null;
                $this->district_id = null;
            }
        }

        if((!is_null($this->city_id) && !empty($this->city_id))
            && (!is_null($this->district_id) && !empty($this->district_id))) {
            $district = District::findOne($this->district_id);
            
            if(is_null($district) || empty($district) || $district->city->id != $this->city_id) {
                $this->district_id = null;
            }
        }

        return parent::beforeValidate();
    }

    /**
     * Process request's data
     * 
     * @return boolean
     */
    public function process()
    {
        $this->logo = UploadedFile::getInstance($this, 'logo');
        $this->master_plan = UploadedFile::getInstance($this, 'master_plan');
        $this->images = UploadedFile::getInstances($this, 'images');
        
        if (!$this->validate()) {
            return false;
        }
        
        $this->processFile('logo');
        $this->processFile('master_plan');
        $this->processFiles('images');

        return true;
    }
}
