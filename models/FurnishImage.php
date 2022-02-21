<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "furnish_image".
 *
 * @property int $id
 * @property int $furnish_id
 * @property string|null $image
 *
 * @property Furnish $furnish
 */
class FurnishImage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'furnish_image';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['furnish_id'], 'required'],
            [['furnish_id'], 'integer'],
            [['image'], 'string', 'max' => 200],
            [['furnish_id'], 'exist', 'skipOnError' => true, 'targetClass' => Furnish::className(), 'targetAttribute' => ['furnish_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'furnish_id' => 'Furnish ID',
            'image' => 'Изображение',
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

        if (!empty($this->image)) {
            unlink(Yii::getAlias("@webroot/uploads/$this->image"));
        }

        return true;
    }

    /**
     * Gets query for [[Furnish]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFurnish()
    {
        return $this->hasOne(Furnish::className(), ['id' => 'furnish_id']);
    }
}
