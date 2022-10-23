<?php

namespace app\models;

use Yii;
use app\components\traits\FillAttributes;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "commercial".
 *
 * @property int $id
 * @property int $commercial_id
 * @property string $sent_by
 * @property string $email
 * @property string $sent_at
 * 
 * @property Commercial Commercial
 */
class Commercial extends ActiveRecord
{

    use FillAttributes;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'commercial_history';
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['sent_at'],
                ],
                'value' => new \yii\db\Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['commercial_id'], 'required'],
            [['commercial_id'], 'integer'],
            [['sent_by', 'email'], 'string'],
            [['sent_at'], 'safe'],
            [['initiator_id'], 'exist', 'skipOnError' => true, 'targetClass' => Commercial::className(), 'targetAttribute' => ['commercial_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'commercial_id' => 'Commercial ID',
            'sent_by' => 'Отправлено по',
            'email' => 'Почта',
            'sent_at' => 'Дата отправки',
        ];
    }

    /**
     * Gets query for [[Commercial]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCommercial()
    {
        return $this->hasOne(Commercial::className(), ['id' => 'commercial_id']);
    }
  
}