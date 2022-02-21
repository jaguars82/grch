<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "building_type".
 *
 * @property int $id
 * @property string|null $name
 */
class BuildingType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'building_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'short_name'], 'required'],
            [['name'], 'unique'],
            [['name', 'short_name'], 'string', 'max' => 200]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Тип здания',
            'short_name' => 'Короткая запись'
        ];
    }

    
    /**
     * Get all building types in array form
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
        
        $buildingTypes = [];
        
        foreach ($result as $key => $buildingType) {
            $buildingTypes[$key] = $buildingType['name'];
        }
        
        return $buildingTypes;
    }
}
