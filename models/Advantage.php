<?php

namespace app\models;

use Yii;
use app\components\traits\FillAttributes;

/**
 * This is the model class for table "advantage".
 *
 * @property int $id
 * @property string|null $name
 */
class Advantage extends \yii\db\ActiveRecord
{
    use FillAttributes;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'advantage';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'icon'], 'required'],
            [['name', 'icon'], 'unique'],
            [['name', 'icon'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Преимущество',
            'icon' => 'Иконка'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        if (!empty($this->icon)) {
            unlink(Yii::getAlias("@webroot/uploads/$this->icon"));
        }

        return true;
    }

    /**
     * Get all advantages in array form
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
        
        $advantages = [];
        
        foreach ($result as $key => $advantage) {
            $advantages[$key] = $advantage['name'];
        }
        
        return $advantages;
    }
}
