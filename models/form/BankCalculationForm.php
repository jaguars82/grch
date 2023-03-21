<?php

namespace app\models\form;

use app\components\traits\FillAttributes;
use yii\base\Model;

/**
 * Form for bank's data
 */
class BankCalculationForm extends Model
{
    use FillAttributes;
    
    public $name;
    public $is_logo_reset = 0;
    public $yearly_rate;
    public $initial_fee_rate;
    public $payment_period;
    public $tariff_id;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'payment_period'], 'required'],
            [['name'], 'string', 'max' => 200],
            [['payment_period', 'yearlyRateAsPercent', 'initialFeeRateAsPercent', 'tariff_id'], 'integer'],
            [['yearly_rate', 'initial_fee_rate'], 'double']
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Название',
            'yearly_rate' => 'Процентная ставка годовых',
            'initialFeeRateAsPercent' => 'Первоначальный взнос',
            'payment_period' => 'Срок ипотеки',
            'yearlyRateAsPercent' => 'Процентная ставка годовых',
            'tariff_id' => 'Тариф'
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
}
