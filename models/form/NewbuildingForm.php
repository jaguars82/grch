<?php

namespace app\models\form;

use app\components\traits\FillAttributes;
use app\models\Newbuilding;
use yii\base\Model;
use app\models\City;
use app\models\District;

/**
 * Form for newbuilding's data
 */
class NewbuildingForm extends Model
{
    use FillAttributes {
        fill as protected originFill;
    }
    
    const SCENARIO_UPDATE = 'update';
    
    public $newbuilding_complex_id;
    public $name;
    public $region_id;
    public $city_id;
    public $district_id;
    public $street_type_id;
    public $street_name;
    public $building_type_id;
    public $building_number;
    public $longitude;
    public $latitude;
    public $detail;
    public $total_floor;
    public $material;
    public $status = Newbuilding::STATUS_PROCESS;
    public $deadline;
    public $advantages = [];
    
    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        $commonFields = [
            'name', 'detail', 'total_floor', 'status', 'deadline', 'material', 'city_id', 'building_number',
            'region_id', 'district_id', 'street_type_id', 'building_type_id', 'street_name', 'advantages'
        ];
        
        return [
            self::SCENARIO_DEFAULT => array_merge($commonFields, ['newbuilding_complex_id', 'longitude', 'latitude']),
            self::SCENARIO_UPDATE => $commonFields,
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['advantages'], 'safe'],
            [['newbuilding_complex_id', 'total_floor', 'status'], 'required'],
            [['longitude', 'latitude'], 'double'],
            [['detail', 'deadline'], 'string'],
            [['name', 'material', 'street_name'], 'string', 'max' => 200],
            [['newbuilding_complex_id', 'total_floor', 'status', 'building_type_id', 'street_type_id', 'district_id', 'city_id', 'region_id'], 'integer'],
            [['building_number'], 'string', 'max' => 20],
            [['deadline', 'detail', 'material'], 'default', 'value' => NULL],
            [['building_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\BuildingType::className(), 'targetAttribute' => ['building_type_id' => 'id']],
            [['street_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\StreetType::className(), 'targetAttribute' => ['street_type_id' => 'id']],
            [['district_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\District::className(), 'targetAttribute' => ['district_id' => 'id']],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\Region::className(), 'targetAttribute' => ['region_id' => 'id']],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\City::className(), 'targetAttribute' => ['city_id' => 'id']],
            [['newbuilding_complex_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\NewbuildingComplex::className(), 'targetAttribute' => ['newbuilding_complex_id' => 'id']],
        ];
    }
 
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'newbuilding_complex_id' => 'Жилой комплекс',
            'name' => 'Название',
            'street_name' => 'Название улицы',
            'detail' => 'Информация',
            'total_floor' => 'Количество этажей',
            'deadline' => 'Срок сдачи',
            'material' => 'Материал',
            'status' => 'Статус',
            'building_number' => 'Номер здания',
            'advantages' => 'Преимущество'
        ];
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
     * {@inheritdoc}
     */
    public function fill($data = [], $exceptFields = [])
    {
        $result = $this->originFill($data, $exceptFields);
        
        if (!is_null($this->deadline)) {
            $this->deadline = \Yii::$app->formatter->asDate(strtotime($this->deadline . ' + 1 seconds'), 'php:d.m.Y');
        }
        
        return $result;
    }
    
    /**
     * Process request's data
     * 
     * @return boolean
     */
    public function process()
    {
        if (!$this->validate()) {
            return false;
        }
        
        if (!is_null($this->deadline)) {
            $this->deadline = \Yii::$app->formatter->asDate($this->deadline, 'php:Y-m-d H:i:s');
        }

        return true;
    }
}
