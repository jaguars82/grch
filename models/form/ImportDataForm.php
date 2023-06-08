<?php

namespace app\models\form;

use app\components\import\ImportServiceInterface;
use app\components\traits\FillAttributes;
use app\models\Import;
use yii\base\Model;

/**
 * Form for import's data
 */
class ImportDataForm extends Model
{
    use FillAttributes {
        fill as protected originFill;
    }
    
    const SCENARIO_UPDATE = 'update';
    
    const PERIOD_TYPE_MINUTE = 0;    
    const PERIOD_TYPE_HOUR = 1;
    const PERIOD_TYPE_DAY = 2;
    
    public static $periodType = [
        self::PERIOD_TYPE_MINUTE => 'Минута',
        self::PERIOD_TYPE_HOUR => 'Час',
        self::PERIOD_TYPE_DAY => 'День',
    ];
    
    public $algorithm;
    public $type;
    public $endpoint;
    public $schedule;    
    public $period;
    public $period_type;
    
    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        $commonFields = [
            'algorithm', 'type','endpoint', 'schedule', 'period_type'
        ];
        
        return [
            self::SCENARIO_DEFAULT => array_merge($commonFields, ['period']),
            self::SCENARIO_UPDATE => $commonFields,
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $arrayImport = implode(',', array_keys(Import::$import_types));
        $arrayAutoImport = implode(',', Import::$import_auto);
        
        return [
            [['type', 'period_type'], 'integer'],
            [['algorithm', 'endpoint', 'period'], 'string'],
            [
                'algorithm', 'required', 'when' => function ($model) {
                    return in_array($model->type, array_keys(Import::$import_types));
                }, 'whenClient' => "function (attribute, value) {
                    return [$arrayImport].indexOf(parseInt($('#import-type option:selected').val())) != -1
                }",
                'skipOnError' => false,
            ],
            ['algorithm', \app\components\validators\ImportValidator::className(), 'skipOnError' => false],
            [
                ['endpoint'/*, 'period', 'period_type'*/], 'required', 'when' => function ($model) {
                    return in_array($model->type, Import::$import_auto);
                }, 'whenClient' => "function (attribute, value) {
                    return [$arrayAutoImport].indexOf(parseInt($('#import-type option:selected').val())) != -1
                }"
            ],
            [['endpoint'], 'default', 'value' => NULL],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'algorithm' => 'Класс, реализующий импорт',
            'type' => 'Тип импорта',
            'endpoint' => 'Адрес расположения документа с данными импорта',
            'period' => 'Период импорта',
            'period_type' => 'Тип периода импорта',
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function fill($data = [], $exceptFields = [])
    {
        $result = $this->originFill($data, $exceptFields);
        
        if (isset($data['schedule'])) {
            $this->parseShedule();
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
        $this->processShedule();
        
        if ($this->type == 0) {
            $this->type = NULL;
        }
        
        if (!in_array($this->type, Import::$import_auto)) {
            $this->schedule = NULL;
        }
        
        if (!$this->validate()) {
            return false;
        }

        return true;
    }
    
    /**
     * Getting period and period type from schedule attribute
     */
    public function parseShedule()
    {
        if ($this->schedule >= 86400) {
            if ($this->schedule % 24 === 0) {
                $this->period_type = self::PERIOD_TYPE_DAY;
                $this->period = $this->schedule / 86400;
            } else if ($this->schedule % 3600 === 0) {
                $this->period_type = self::PERIOD_TYPE_HOUR;
                $this->period = $this->schedule / 3600;
            } else {
                $this->period_type = self::PERIOD_TYPE_MINUTE;
                $this->period = $this->schedule / 60;
            }
        } else if ($this->schedule >= 3600) {
            if ($this->schedule % 3600 === 0) {
                $this->period_type = self::PERIOD_TYPE_HOUR;
                $this->period = $this->schedule / 3600;
            } else {
                $this->period_type = self::PERIOD_TYPE_MINUTE;
                $this->period = $this->schedule / 60;
            }
        } else {
            $this->period_type = self::PERIOD_TYPE_MINUTE;
            $this->period = $this->schedule / 60;
        }
    }
    
    /**
     * Setting up schedule attribute from period and period type
     */
    public function processShedule()
    {
        switch ((int)$this->period_type) {
            case self::PERIOD_TYPE_MINUTE:
                $this->schedule = (int)$this->period * 60;
                break;
            case self::PERIOD_TYPE_HOUR:
                $this->schedule = (int)$this->period * 3600;
                break;
            case self::PERIOD_TYPE_DAY:
                $this->schedule = (int)$this->period * 86400;
                break;
        }
    }
}
