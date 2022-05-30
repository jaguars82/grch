<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "entrance".
 *
 * @property int $id
 * @property int $newbuilding_id
 * @property string|null $name
 * @property int|null $number
 * @property int|null $floors
 * @property string|null $materil
 * @property int|null $azimuth
 * @property float|null $longitude
 * @property float|null $latitude
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Newbuilding $newbuilding
 * @property Flat[] $flats
 */
class Entrance extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'entrance';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['newbuilding_id'], 'required'],
            [['newbuilding_id', 'number', 'floors', 'azimuth'], 'integer'],
            [['longitude', 'latitude'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 200],
            [['materil'], 'string', 'max' => 255],
            [['newbuilding_id'], 'exist', 'skipOnError' => true, 'targetClass' => Newbuilding::className(), 'targetAttribute' => ['newbuilding_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'newbuilding_id' => 'Newbuilding ID',
            'name' => 'Название',
            'number' => 'Номер',
            'floors' => 'Количество этажей',
            'materil' => 'Материал',
            'azimuth' => 'Азимут',
            'longitude' => 'Долгота',
            'latitude' => 'Широта',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Newbuilding]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNewbuilding()
    {
        return $this->hasOne(Newbuilding::className(), ['id' => 'newbuilding_id']);
    }

    /**
     * Gets query for [[Flats]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFlats()
    {
        return $this->hasMany(Flat::className(), ['entrance_id' => 'id']);
    }
}
