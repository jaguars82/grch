<?php

namespace app\models;

use Yii;
use app\models\Region;

/**
 * This is the model class for table "region_district".
 *
 * @property int $id
 * @property string|null $name
 *
 * @property City[] $cities
 */

class RegionDistrict extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'region_district';
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
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Region::className(), 'targetAttribute' => ['region_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Район субъекта РФ',
            'region_id' => 'Регион'
        ];
    }

    /**
     * Get all districts in array form
     * 
     * @return array
     */
    public static function getAllAsList()
    {
        $result = self::find()
            ->orderBy(['name' => SORT_DESC])
            ->indexBy('id')
            ->asArray()
            ->all();
        
        $regionDistricts = [];
        
        foreach ($result as $key => $region_district) {
            $regionDistricts[$key] = $region_district['name'];
        }
        
        return $regionDistricts;
    }

    /**
     * Get all districts for a particular region in array
     * 
     * @return array
     */
    public static function getForRegionAsList($regionId)
    {
        $result = self::find()
            ->where(['region_id' => $regionId])
            ->orderBy(['name' => SORT_ASC])
            ->indexBy('id')
            ->asArray()
            ->all();
        
        $regionDistricts = [];
        
        foreach ($result as $key => $region_district) {
            $regionDistricts[$key] = $region_district['name'];
        }

        return $regionDistricts;
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
     * Gets query for [[City]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCities()
    {
        return $this->hasMany(City::className(), ['region_district_id' => 'id'])->orderBy(['name' => SORT_ASC]);
    }

    /**
     * Gets query for [[City]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCenter()
    {
        return City::findOne(['region_district_id' => $this->id, 'is_district_center' => 1]);
    }
}