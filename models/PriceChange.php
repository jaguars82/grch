<?php
namespace app\models;

use yii\db\ActiveRecord;

class PriceChange extends ActiveRecord
{
    const NO_MOVEMENT = 0;
    const MOVEMENT_UP = 1;
    const MOVEMENT_DOWN = 2;

    public static function tableName()
    {
        return '{{%price_change}}';
    }

    public function rules()
    {
        return [
            [['flat_id'], 'required'],
            [['flat_id'], 'integer'],
            [['price_cash', 'unit_price_cash', 'price_credit', 'unit_price_credit'], 'number'],
            [['area_snapshot', 'movement'], 'number'],
            [['price_updated_at'], 'safe'],
            [['is_initial'], 'boolean'],
            ['movement', 'default', 'value' => 0],
            [['flat_id'], 'exist', 'targetClass' => Flat::class, 'targetAttribute' => 'id'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'flat_id' => 'Flat ID',
            'price_cash' => 'Price Cash',
            'unit_price_cash' => 'Unit Price Cash',
            'price_credit' => 'Price Credit',
            'unit_price_credit' => 'Unit Price Credit',
            'area_snapshot' => 'Area Snapshot',
            'price_updated_at' => 'Price Updated At',
            'is_initial' => 'Is Initial',
        ];
    }

    public function getFlat()
    {
        return $this->hasOne(Flat::class, ['id' => 'flat_id']);
    }
}