<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "secondary_renovation".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $alias alternative name (e.g. in latin or from outer source (feed))
 * @property string|null $detail
 *
 * @property SecondaryRoom[] $secondaryRooms
 */
class SecondaryRenovation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'secondary_renovation';
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
            'name' => 'Название',
            'alias' => 'Альтернативное название',
            'detail' => 'Описание',
        ];
    }

    /**
     * Gets query for [[SecondaryRooms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSecondaryRooms()
    {
        return $this->hasMany(SecondaryRoom::className(), ['renovation_id' => 'id']);
    }

    /**
     * Gets Secondary renovation by given name
     */
    public static function getRenovationByName($name)
    {
        return static::find()
            ->where(['name' => $name])
            ->orWhere(['alias' => $name])
            ->one();
    }
}