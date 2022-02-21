<?php

namespace app\models;

use Yii;
use app\components\traits\FillAttributes;

/**
 * This is the model class for table "floor_layout".
 *
 * @property int $id
 * @property int $newbuilding_id
 * @property int|null $floor
 * @property int|null $section
 * @property string|null $image
 * @property string|null $map
 *
 * @property Newbuilding $newbuilding
 */
class FloorLayout extends \yii\db\ActiveRecord
{
    use FillAttributes;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'floor_layout';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['newbuilding_id'], 'required'],
            [['newbuilding_id'], 'integer'],
            [['map', 'floor', 'section'], 'string'],
            [['image'], 'string', 'max' => 200],
            [['newbuilding_id'], 'exist', 'skipOnError' => true, 'targetClass' => Newbuilding::className(), 'targetAttribute' => ['newbuilding_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'floor' => 'Этаж',
            'section' => 'Подъезд',
            'image' => 'Изображение',
            'map' => 'Map',
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert) 
    {
        if(!$insert && !empty($this->image)) {
            $model = self::findOne($this->id);

            if($this->image != $model->image) {
                $filePath = \Yii::getAlias('@webroot/uploads/' . $model->image);
                
                if(!is_dir($filePath) && file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }

        return parent::beforeSave($insert);
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

    public function getMapArray()
    {
        preg_match_all('/coords=\"(?<coords>[\d,]+)\"/', $this->map, $match);
        
        return $match['coords'];
    }

    /**
     * Gets query for [[Newbuilding]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNewbuilding()
    {
        return $this->hasOne(Newbuilding::className(), ['id' => 'newbuilding_id']);
    }
}
