<?php

namespace app\models;

/**
 * This is the model class for table "flat_image".
 *
 * @property int $id
 * @property int $flat_id
 * @property string|null $image
 *
 * @property Flat $flat
 */
class FlatImage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'flat_image';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['flat_id'], 'required'],
            [['flat_id'], 'integer'],
            [['image'], 'string', 'max' => 200],
            [['flat_id'], 'exist', 'skipOnError' => true, 'targetClass' => Flat::className(), 'targetAttribute' => ['flat_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'flat_id' => 'Flat ID',
            'image' => 'Изображение',
        ];
    }

    /**
     * Gets query for [[Flat]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFlat()
    {
        return $this->hasOne(Flat::className(), ['id' => 'flat_id']);
    }
}
