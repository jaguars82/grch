<?php

namespace app\models;

use Yii;
use app\components\traits\FillAttributes;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "entrance".
 *
 * @property int $id
 * @property int $newbuilding_id
 * @property string|null $name
 * @property int|null $number
 * @property int|null $floors
 * @property string|null $material
 * @property int|null $azimuth
 * @property float|null $longitude
 * @property float|null $latitude
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Newbuilding $newbuilding
 * @property Flat[] $flats
 */
class Entrance extends ActiveRecord
{

    use FillAttributes;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'entrance';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new \yii\db\Expression('NOW()'),
            ],
        ];
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
            [['material'], 'string', 'max' => 255],
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
            'material' => 'Материал',
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
