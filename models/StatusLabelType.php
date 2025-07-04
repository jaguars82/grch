<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "status_label_type".
 *
 * @property int $id
 * @property string $name
 * @property string|null $short_name
 *
 */
class StatusLabelType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'status_label_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'short_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название статуса',
            'short_name' => 'Короткое название статуса',
        ];
    }

    /**
     * Get all status label types in array form
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
        
        $labelTypes = [];
        
        foreach ($result as $key => $labelType) {
            $labelTypes[$key] = $labelType['name'];
        }

        return $labelTypes;
    }

    /**
     * Get status label name by given id
     * 
     * @return string
     */
    public static function getNameById($labelId)
    {
        $labelType = self::findOne($labelId);
        return $labelType ? $labelType->name : 'n/a';
    }
}