<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "region".
 *
 * @property int $id
 * @property string|null $name
 */
class Region extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'region';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'unique'],
            [['name'], 'string', 'max' => 200],
        ];
    }

    /**
     * Get all regions in array form
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
        
        $regions = [];
        
        foreach ($result as $key => $region) {
            $regions[$key] = $region['name'];
        }
        
        return $regions;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Регион',
        ];
    }

    /**
     * Gets query for [[Cities]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCities()
    {
        return $this->hasMany(City::className(), ['region_id' => 'id']);
    }

    /**
     * Gets query for [[Cities]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCenter()
    {
        return City::findOne(['region_id' => $this->id, 'is_region_center' => 1]);
    }

    /**
     * Gets query for [[RegionDistricts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegionDistricts()
    {
        return $this->hasMany(RegionDistrict::className(), ['region_id' => 'id'])->orderBy(['name' => SORT_ASC]);
    }
}
