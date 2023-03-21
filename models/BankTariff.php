<?php

namespace app\models;

use app\components\traits\FillAttributes;

/**
 * This is the model class for table "bank_tariff".
 *
 * @property int $id
 * @property int $bank_id
 * @property string $name
 * @property string $logo
 * @property float $yearly_rate
 * @property floot $initial_fee_rate
 * @property int $payment_period
 * 
 * @property float yearlyRateAsPercent
 */

class BankTariff extends \yii\db\ActiveRecord
{
    use FillAttributes;

    public $minYearlyRate;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bank_tariff';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'yearly_rate', 'initial_fee_rate', 'payment_period', 'bank_id'], 'required'],
            [['name'], 'string', 'max' => 200],
            [['bank_id'], 'integer'],
            [['bank_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\Bank::className(), 'targetAttribute' => ['bank_id' => 'id']],
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
            'initial_fee_rate' => 'Первоначальный взнос',
            'payment_period' => 'Срок ипотеки',
            'yearlyRateAsPercent' => 'Процентная ставка годовых',
        ];
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
     * Getter for yearlyRateAsPercent attribute
     * 
     * @return float
     */
    public function getYearlyRateAsPercent()
    {
        return $this->yearly_rate * 100;
    }
    
    /**
     * Calculate initial fee for flat's price
     * 
     * @param type $cost flat's cost
     * @return floot
     */
    public function calculateInitialFee($cost)
    {
        return $this->initial_fee_rate * $cost;
    }
    
    /**
     * Calculate credit monthly payment for default bank's values
     * 
     * @param type $cost flat's cost
     * @return float
     */
    public function calculateDefaultMonthlyPayment($cost)
    {
        return $this->calculateMonthlyPayment($this->payment_period, $cost);
    }
    
    /**
     * Calculate credit monthly payment
     * 
     * @param type $payment_period credit payment's period
     * @param type $cost flat's cost
     * @return float
     */
    public function calculateMonthlyPayment($payment_period, $cost)
    {
        $monthlyRate = $this->yearly_rate / 12;
        $commonRate = (1 + $monthlyRate) ^ $payment_period;
        return ($cost - $this->calculateInitialFee($cost)) * $monthlyRate * $commonRate / ($commonRate - 1);
    }

    /**
     * Gets query for [[Bank]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBank()
    {
        return $this->hasOne(Bank::className(), ['id' => 'bank_id']);
    }
}
