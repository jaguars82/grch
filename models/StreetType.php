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
            [['name', 'short_name'], 'string', 'max' => 200]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Тип улицы',
            'short_name' => 'Короткая запись'
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
}
