<?php

namespace app\models\form;

use app\components\traits\FillAttributes;
use app\models\Entrance;
use yii\base\Model;

/**
 * Form for entrance's data
 */
class EntranceForm extends Model
{
    use FillAttributes {
        fill as protected originFill;
    }
    
    const SCENARIO_UPDATE = 'update';
    
    public $newbuilding_id;
    public $name;
    public $number;
    public $floors;
    public $material;
    public $status;
    public $deadline;
    public $azimuth;
    public $longitude;
    public $latitude;
    
    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        $commonFields = [
            'name', 'number', 'floors', 'material', 'azimuth', 'status', 'deadline'
        ];
        
        return [
            self::SCENARIO_DEFAULT => array_merge($commonFields, ['newbuilding_id', 'longitude', 'latitude']),
            self::SCENARIO_UPDATE => $commonFields
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['newbuilding_id', 'number'], 'required'],
            [['longitude', 'latitude'], 'double'],
            [['name', 'material'], 'string', 'max' => 200],
            [['newbuilding_id', 'floors', 'azimuth'], 'integer'],
            [['material'], 'default', 'value' => NULL],
            [['newbuilding_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\Newbuilding::className(), 'targetAttribute' => ['newbuilding_id' => 'id']],
        ];
    }
 
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'newbuilding_id' => 'ID позиции',
            'name' => 'Название',
            'number' => 'Номер подъезда',
            'azimuth' => 'Азимут',
            'floors' => 'Количество этажей',
            'material' => 'Материал',
            'status' => 'Статус',
            'deadline' => 'Срок сдачи',
        ];
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