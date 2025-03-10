<?php
namespace app\models;

use yii\db\ActiveRecord;

class AreaChange extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%area_change}}';
    }

    public function rules()
    {
        return [
            [['flat_id'], 'required'],
            [['flat_id'], 'integer'],
            [['area'], 'number'],
            [['price_updated_at'], 'safe'],
            [['is_initial'], 'boolean'],
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
