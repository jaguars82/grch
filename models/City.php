<?php

namespace app\models;

use Yii;
use app\models\Region;
use app\models\query\CityQuery;

/**
 * This is the model class for table "city".
 *
 * @property int $id
 * @property string|null $name
 *
 * @property District[] $districts
 */
class City extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'city';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'region_id'], 'required'],
            [['region_id'], 'integer'],
            [['name'], 'string', 'max' => 200],
            [['longitude', 'latitude'], 'double'],
            [['name'], 'unique'],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Region::className(), 'targetAttribute' => ['region_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Город',
            'region_id' => 'Регион'
        ];
    }

    /**
     * Get all cities in array form
     * 
     * @return array
     */
    public static function getAllAsList()
    {
        $result = self::find()
            ->orderBy(['id' => SORT_DESC])
            ->indexBy('id')
            ->asArray()
            ->all();
        
        $cities = [];
        
        foreach ($result as $key => $city) {
            $cities[$key] = $city['name'];
        }
        
        return $cities;
    }

    /**
     * {@inheritdoc}
     * 
     * @return NewbuildingComplexQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CityQuery(get_called_class());
    }

    /**
     * Gets query for [[Region]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Region::className(), ['id' => 'region_id']);
    }

    /**
     * Gets query for [[Districts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDistricts()
    {
        return $this->hasMany(District::className(), ['city_id' => 'id']);
    }
}
