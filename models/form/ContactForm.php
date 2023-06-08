<?php

namespace app\models\form;

use app\components\traits\FillAttributes;
use app\components\traits\ProcessFile;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Form for contact's data
 */
class ContactForm extends Model
{
    use FillAttributes {
        fill as protected originFill;
    }
    use ProcessFile;
    
    const SCENARIO_UPDATE = 'update';
    
    public $type;
    public $person;
    public $phone;
    public $is_phone_reset = 0;
    public $photo;
    public $agency_id;
    public $developer_id;
    public $newbuilding_complex_id;    
    public $is_set_worktime = false;
    public $worktime;
    public $mon_from_date, $mon_to_date;
    public $tue_from_date, $tue_to_date;
    public $wed_from_date, $wed_to_date;
    public $thu_from_date, $thu_to_date;
    public $fri_from_date, $fri_to_date;
    public $sat_from_date, $sat_to_date;
    public $sun_from_date, $sun_to_date;
    
    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        $commonFields = [
            'type', 'person', 'phone', 'is_set_worktime',
            'mon_from_date', 'mon_to_date', 'tue_from_date', 'tue_to_date', 'wed_from_date', 'wed_to_date', 'thu_from_date',
            'thu_to_date', 'fri_from_date', 'fri_to_date', 'sat_from_date', 'sat_to_date', 'sun_from_date', 'sun_to_date',
            'photo', 'is_phone_reset'
        ];
        
        return [
            self::SCENARIO_DEFAULT => array_merge($commonFields, ['agency_id', 'developer_id', 'newbuilding_complex_id']),
            self::SCENARIO_UPDATE => $commonFields,
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'person', 'phone', 'is_set_worktime'], 'required'],
            [['type', 'person'], 'string', 'max' => 200],
            [['phone'], 'string', 'max' => 255],
            [['agency_id', 'developer_id', 'newbuilding_complex_id'], 'integer'],
            [['is_set_worktime'], 'boolean'],
            [['photo'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, gif, jpeg, svg'],
            [['mon_from_date', 'mon_to_date', 'tue_from_date', 'tue_to_date', 'wed_from_date', 'wed_to_date', 'thu_from_date', 'thu_to_date',], 'string'],
            [['fri_from_date', 'fri_to_date', 'sat_from_date', 'sat_to_date', 'sun_from_date', 'sun_to_date'], 'string'],
            [['agency_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\Agency::className(), 'targetAttribute' => ['agency_id' => 'id']],
            [['developer_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\Developer::className(), 'targetAttribute' => ['developer_id' => 'id']],
            [['newbuilding_complex_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\NewbuildingComplex::className(), 'targetAttribute' => ['newbuilding_complex_id' => 'id']],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'type' => 'Тип',
            'person' => 'Сотрудник',
            'phone' => 'Телефон',
            'photo' => 'Фотография',
            'is_set_worktime' => 'Указать время работы',
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function fill($data = [], $exceptFields = [])
    {
        $result = $this->originFill($data, $exceptFields);
        
        if (isset($data['worktime']) && !empty($data['worktime'])) {
            $this->setWorktime($data['worktime']);
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
        $this->photo = UploadedFile::getInstance($this, 'photo');
        
        if (!$this->validate()) {
            return false;
        }
        
        $this->processFile('photo');
        $this->worktime = $this->is_set_worktime ? $this->getWorktime() : NULL;

        return true;
    }
    
    /**
     * Setting up worktime's attributes from formatted string
     * 
     * @param string $worktime worktime string
     */
    public function setWorktime($worktime)
    {
        $this->is_set_worktime = true;
        
        foreach (explode(',', $worktime) as $day) {
            list($weekday, $from_time, $to_time) = explode('-', $day);
            if ($weekday == 'mon') {
                $this->mon_from_date = $from_time;
                $this->mon_to_date = $to_time;
            } else if ($weekday == 'tue') {
                $this->tue_from_date = $from_time;
                $this->tue_to_date = $to_time;
            } else if ($weekday == 'wed') {
                $this->wed_from_date = $from_time;
                $this->wed_to_date = $to_time;
            } else if ($weekday == 'thu') {
                $this->thu_from_date = $from_time;
                $this->thu_to_date = $to_time;
            } else if ($weekday == 'fri') {
                $this->fri_from_date = $from_time;
                $this->fri_to_date = $to_time;
            } else if ($weekday == 'sat') {
                $this->sat_from_date = $from_time;
                $this->sat_to_date = $to_time;
            } else if ($weekday == 'sun') {
                $this->sun_from_date = $from_time;
                $this->sun_to_date = $to_time;
            }
        }
    }
    
    /**
     * Getting formatted string from worktime's attrubutes
     * 
     * @return mixed
     */
    private function getWorktime()
    {
        $worktime = "";            
        if (!empty($this->mon_from_date) && !empty($this->mon_to_date)) {
            $worktime .= ",mon-{$this->mon_from_date}-{$this->mon_to_date}";
        }
        if (!empty($this->tue_from_date) && !empty($this->tue_to_date)) {
            $worktime .= ",tue-{$this->tue_from_date}-{$this->tue_to_date}";
        }
        if (!empty($this->wed_from_date) && !empty($this->wed_to_date)) {
            $worktime .= ",wed-{$this->wed_from_date}-{$this->wed_to_date}";
        }
        if (!empty($this->thu_from_date) && !empty($this->thu_to_date)) {
            $worktime .= ",thu-{$this->thu_from_date}-{$this->thu_to_date}";
        }
        if (!empty($this->fri_from_date) && !empty($this->fri_to_date)) {
            $worktime .= ",fri-{$this->fri_from_date}-{$this->fri_to_date}";
        }
        if (!empty($this->sat_from_date) && !empty($this->sat_to_date)) {
            $worktime .= ",sat-{$this->sat_from_date}-{$this->sat_to_date}";
        } 
        if (!empty($this->sun_from_date) && !empty($this->sun_to_date)) {
            $worktime .= ",sun-{$this->sun_from_date}-{$this->sun_to_date}";
        }
        
        return strlen($worktime) ? substr($worktime, 1) : NULL;
    }
}
