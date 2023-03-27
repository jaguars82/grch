<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "secondary_property_type".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $alias alternative name (e.g. in latin or from outer source (feed))
 *
 * @property SecondaryRoom[] $secondaryRooms
 */
class SecondaryPropertyType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'secondary_property_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
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
        ];
    }

    /**
     * Gets query for [[SecondaryRooms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSecondaryRooms()
    {
        return $this->hasMany(SecondaryRoom::className(), ['property_type_id' => 'id']);
    }


    /**
     * Gets Secondary property type by given name
     */
    public static function getPropertyTypeByName($name)
    {
        return static::find()
            ->where(['name' => $name])
            ->orWhere(['alias' => $name])
            ->one();
    }    
}