<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "building_material".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $alias
 * @property string|null $detail
 *
 * @property SecondaryRoom[] $secondaryRooms
 */
class BuildingMaterial extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'building_material';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['detail'], 'string'],
            [['name', 'alias'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'alias' => 'Alias',
            'detail' => 'Detail',
        ];
    }

    /**
     * Gets query for [[SecondaryRooms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSecondaryRooms()
    {
        return $this->hasMany(SecondaryRoom::className(), ['material_id' => 'id']);
    }

    /**
     * Gets building material by given name
     */
    public static function getMaterialByName($name)
    {
        return static::find()
            ->where(['name' => $name])
            ->orWhere(['alias' => $name])
            ->one();
    }
    
    /**
     * Gets array of materials
     */
    public static function getMaterialList()
    {
        return static::find()
            ->orderBy(['name' => SORT_ASC])
            ->asArray()
            ->all();
    }
}
