<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use app\components\traits\FillAttributes;

/**
 * This is the model class for table "document".
 */
class Document extends ActiveRecord
{
    use FillAttributes;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'document';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new \yii\db\Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Название',
            'file' => 'Документ',
            'size' => 'Размер',
            'created_at' => 'Дата загрузки'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['size', 'double'],
            [['file'], 'unique'],
            [['name'], 'required'],
            [['file', 'name'], 'string', 'max' => 200],
            [['created_at', 'updated_at'], 'safe'],
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

        if (!empty($this->file)) {
            unlink(Yii::getAlias("@webroot/uploads/$this->file"));
        }

        return true;
    }

    /**
     * Gets query for [[NewbuildingComplex]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNewbuildingComplex()
    {
        return $this->hasOne(NewbuildingComplex::className(), ['id' => 'newbuilding_complex_id'])
                ->viaTable('newbuilding_complex_document', ['document_id' => 'id']);
    }
}