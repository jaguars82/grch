<?php

namespace app\models;

use app\components\traits\FillAttributes;

/**
 * This is the model class for table "furnish".
 *
 * @property int $id
 * @property int $newbuilding_complex_id
 * @property string|null $name
 * @property string|null $detail
 *
 * @property FurnishImage[] $furnishImages
 * @property NewbuildingComplex $newbuildingComplex
 */
class Furnish extends \yii\db\ActiveRecord
{
    use FillAttributes;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'furnish';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['newbuilding_complex_id'], 'required'],
            [['newbuilding_complex_id'], 'integer'],
            [['detail'], 'string'],
            [['name'], 'string', 'max' => 200],
            [['newbuilding_complex_id'], 'exist', 'skipOnError' => true, 'targetClass' => NewbuildingComplex::className(), 'targetAttribute' => ['newbuilding_complex_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'newbuilding_complex_id' => 'Newbuilding Complex ID',
            'name' => 'Название',
            'detail' => 'Информация',
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

        $imageList = $this->furnishImages;
        foreach ($imageList as $image) {
            $image->delete();
        }

        return true;
    }
    
    /**
     * Gets query for [[FurnishImages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFurnishImages()
    {
        return $this->hasMany(FurnishImage::className(), ['furnish_id' => 'id']);
    }

    /**
     * Gets query for [[NewbuildingComplex]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNewbuildingComplex()
    {
        return $this->hasOne(NewbuildingComplex::className(), ['id' => 'newbuilding_complex_id']);
    }
}
