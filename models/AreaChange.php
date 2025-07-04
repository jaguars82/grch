<?php
namespace app\models;

use yii\db\ActiveRecord;

class AreaChange extends ActiveRecord
{
    const NO_MOVEMENT = 0;
    const MOVEMENT_UP = 1;
    const MOVEMENT_DOWN = 2;

    public static function tableName()
    {
        return '{{%area_change}}';
    }

    public function rules()
    {
        return [
            [['flat_id'], 'required'],
            [['flat_id'], 'integer'],
            [['area', 'movement'], 'number'],
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
            'area' => 'Area',
            'price_updated_at' => 'Price Updated At',
            'is_initial' => 'Is Initial',
        ];
    }

    public function getFlat()
    {
        return $this->hasOne(Flat::class, ['id' => 'flat_id']);
    }
}
