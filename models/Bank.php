<?php

namespace app\models;

use Yii;
use app\components\traits\FillAttributes;

/**
 * This is the model class for table "bank".
 *
 * @property int $id
 * @property string $name
 * @property string $logo
 *
 * @property BankNewbuildingComplex[] $bankNewbuildingComplexes
 */
class Bank extends \yii\db\ActiveRecord
{
    use FillAttributes;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bank';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'logo'], 'required'],
            [['name', 'logo'], 'string', 'max' => 200],
            [['address', 'email', 'phone', 'url'], 'string'],
            [['email'], 'email'],
            [['longitude', 'latitude'], 'double'],
            [['name'], 'unique'],
            [['logo'], 'unique'],
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
     * {@inheritdoc}
     */
    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        if (!empty($this->logo)) {
            unlink(Yii::getAlias("@webroot/uploads/$this->logo"));
        }

        return true;
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
     * @param array $tariff
     * @param type $cost flat's cost
     * @return floot
     */
    public function calculateInitialFee($tariff, $cost)
    {
        return $tariff['initial_fee_rate'] * $cost;
    }
    
    /**
     * Calculate credit monthly payment for default bank's values
     * 
     * @param array $tariff
     * @param type $cost flat's cost
     * @return float
     */
    public function calculateDefaultMonthlyPayment($tariff, $cost)
    {
        return $this->calculateMonthlyPayment($tariff, $tariff['payment_period'],  $cost);
    }
    
    /**
     * Calculate credit monthly payment
     * 
     * @param array $tariff
     * @param type $payment_period credit payment's period
     * @param type $cost flat's cost
     * @return float
     */
    public function calculateMonthlyPayment($tariff, $payment_period, $cost)
    {
        $monthlyRate = $tariff['yearly_rate'] / 12;
        $commonRate = (1 + $monthlyRate) ^ $payment_period;
        return ($cost - $this->calculateInitialFee($tariff, $cost)) * $monthlyRate * $commonRate / ($commonRate - 1);
    }

    /**
     * Gets query for [[BankTariff]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTariffs()
    {
        return $this->hasMany(BankTariff::className(), ['bank_id' => 'id']);
    }
    
    /**
     * Gets query for [[Bank]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNewbuildingComplexes()
    {
        return $this->hasMany(NewbuildingComplex::className(), ['id' => 'newbuilding_complex_id'])
                ->viaTable('bank_newbuilding_complex', ['bank_id' => 'id']);
    }

    public function getMinYearlyRate()
    {
        $result = $this->getTariffs()->select([
            'min(yearly_rate) as minYearlyRate'
        ])->one();

        return isset($result->minYearlyRate)  && !empty($result->minYearlyRate) ? $result->minYearlyRate : NULL;
    }
}
