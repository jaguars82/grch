<?php

namespace app\models;

use Yii;
use app\models\Region;
use app\models\RegionDistrict;
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
            [['region_id', 'region_district_id', 'is_region_center', 'is_district_center'], 'integer'],
            [['name'], 'string', 'max' => 200],
            [['longitude', 'latitude'], 'double'],
            [['name'], 'unique'],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Region::className(), 'targetAttribute' => ['region_id' => 'id']],
            [['region_district_id'], 'exist', 'skipOnError' => true, 'targetClass' => RegionDistrict::className(), 'targetAttribute' => ['region_district_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Город',
            'region_id' => 'Регион',
            'is_region_center' => 'Региональный центр',
            'is_district_center' => 'Районный центр',
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
     * Get cities in a given region with newbuilding complexes in array form
     * 
     * @return array
     */
    public static function getForRegionWithNewbuildingComplexesAsList($regionId)
    {
        $cities = self::find()
        ->innerJoin('newbuilding_complex', 'newbuilding_complex.city_id = city.id')
        ->select(['city.id', 'city.name', 'is_region_center'])
        ->where(['newbuilding_complex.region_id' => $regionId])
        ->orderBy(['city.name' => SORT_ASC])
        ->asArray()
        ->all();
        
        $regionCenter = [];
        $sattlements = [];

        foreach ($cities as $city) {
            if ($city['is_region_center'] == 1) {
                array_push($regionCenter, $city);
            } else {
                array_push($sattlements, $city);
            }
        }

        $result = count($regionCenter) > 0 ? array_merge($regionCenter, $sattlements) : $sattlements;
        
        return $result;
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
     * Gets query for [[RegionDistrict]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegiondistrict()
    {
        return $this->hasOne(RegionDistrict::className(), ['id' => 'region_district_id']);
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

    /**
     * Gets city by given name
     */
    public static function getCityByRegionAndName($regionId, $name)
    {
        return static::find()
            ->where(['region_id' => $regionId])
            ->andWhere(['like', 'name', '%'.$name.'%', false])
            ->one();
    }
}
