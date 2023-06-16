<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "street_type".
 *
 * @property int $id
 * @property string|null $name
 */
class StreetType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'street_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'short_name'], 'required'],
            [['name'], 'unique'],
            [['name', 'short_name'], 'string', 'max' => 200],
            [['aliases'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Тип улицы',
            'short_name' => 'Короткая запись',
            'aliases' => 'Варианты',
        ];
    }

    
    /**
     * Get all street types in array form
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
        
        $streetTypes = [];
        
        foreach ($result as $key => $streetType) {
            $streetTypes[$key] = $streetType['name'];
        }

        return $streetTypes;
    }
    
    /**
     * Get all street type aliases in array form
     * 
     * @return array
     */
    public static function getAliasesAsList()
    {
        $result = self::find()
            ->orderBy(['id' => SORT_DESC])
            ->indexBy('id')
            ->asArray()
            ->all();
        
        $streetAliases = [];
        
        foreach ($result as $key => $streetType) {
            $streetAliases[$key] = $streetType['aliases'];
        }

        return $streetAliases;
    }

    /**
     * Get street complex types (containing more then 1 word) in array form
     * 
     * @return array
     */
    public static function getComplexTypesAsList()
    {
        $result = self::find()
            ->orderBy(['id' => SORT_DESC])
            ->indexBy('id')
            ->asArray()
            ->all();
        
        $streetComplexTypes = [];
        
        foreach ($result as $key => $streetType) {
            $parts = explode(' ', $streetType['name']);
            if (count($parts) > 1) {
               $streetComplexTypes[$key] = $streetType['name']; 
            }
        }

        return $streetComplexTypes;
    }
}
