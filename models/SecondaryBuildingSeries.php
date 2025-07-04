<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "secondary_building_series".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $alias alternative name (e.g. in latin or from outer source (feed))
 * @property string|null $detail
 *
 * @property SecondaryRoom[] $secondaryRooms
 */
class SecondaryBuildingSeries extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'secondary_building_series';
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
        return $this->hasMany(SecondaryRoom::className(), ['building_series_id' => 'id']);
    }

    
    /**
     * Gets Secondary building series by given name
     */
    public static function getBuildingSeriesByName($name)
    {
        return static::find()
            ->where(['name' => $name])
            ->orWhere(['alias' => $name])
            ->one();
    }

    /**
     * Gets array of buildings' series
     */
    public static function getBuildingSeriesList()
    {
        return static::find()
            ->orderBy(['name' => SORT_ASC])
            ->asArray()
            ->all();
    }
}
