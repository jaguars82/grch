<?php

namespace app\models\form;

use app\components\traits\FillAttributes;
use app\components\traits\ProcessFile;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Form for bank's data
 */
class BankTariffForm extends Model
{
    use FillAttributes;
    use ProcessFile;
    
    const SCENARIO_UPDATE = 'update';
    
    public $name;
    public $logo;
    public $is_logo_reset = 0;
    public $yearly_rate;
    public $initial_fee_rate;
    public $payment_period;
    public $bank_id;
    
    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        $commonFields = ['bank_id', 'name', 'yearly_rate', 'yearlyRateAsPercent', 'initial_fee_rate', 'initialFeeRateAsPercent', 'payment_period', 'logo', 'is_logo_reset'];
        
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
            [['name', 'yearlyRateAsPercent', 'initialFeeRateAsPercent', 'payment_period', 'bank_id'], 'required'],
            [['name'], 'string', 'max' => 200],
            [['payment_period', 'bank_id'], 'integer'],
            [['yearlyRateAsPercent', 'initialFeeRateAsPercent'], 'double'],
            [['bank_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\Bank::className(), 'targetAttribute' => ['bank_id' => 'id']],
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
            'yearly_rate' => 'Процентная ставка годовых',
            'initialFeeRateAsPercent' => 'Первоначальный взнос',
            'payment_period' => 'Срок ипотеки',
            'yearlyRateAsPercent' => 'Процентная ставка годовых',
        ];
    }
    
    /**
     * Getter for yearlyRateAsPercent attribute
     * 
     * @return mixed
     */
    public function getYearlyRateAsPercent()
    {
        return is_null($this->yearly_rate) ? NULL : $this->yearly_rate * 100;
    }
    
    /**
     * Getter for yearlyRateAsPercent attribute
     * 
     * @param fload $value
     */
    public function setYearlyRateAsPercent($value)
    {
        $this->yearly_rate = $value / 100;
    }
    
    /**
     * Getter for initialFeeRateAsPercent attribute
     * 
     * @return float
     */
    public function getInitialFeeRateAsPercent()
    {
        return is_null($this->initial_fee_rate) ? NULL : $this->initial_fee_rate * 100;
    }
    
    /**
     * Getter for initialFeeRateAsPercent attribute
     * 
     * @param float $value
     */
    public function setInitialFeeRateAsPercent($value)
    {
        $this->initial_fee_rate = $value / 100;
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
