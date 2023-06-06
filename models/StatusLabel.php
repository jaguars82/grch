<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "status_label".
 *
 * @property int $id
 * @property int $label_type_id
 * @property int|null $is_active
 * @property int|null $creator_id
 * @property string|null $created_at
 * @property int|null $has_expiration_date
 * @property string|null $expires_at
 *
 * @property User $creator
 * @property StatusLabelType $labelType
 */
class StatusLabel extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'status_label';
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
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
            [['label_type_id'], 'required'],
            [['label_type_id', 'creator_id'], 'integer'],
            [['is_active', 'has_expiration_date'], 'boolean'],
            [['created_at', 'expires_at'], 'safe'],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['creator_id' => 'id']],
            [['label_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => StatusLabelType::className(), 'targetAttribute' => ['label_type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'label_type_id' => 'Тип',
            'is_active' => 'Активен',
            'creator_id' => 'Добавил',
            'created_at' => 'Время добавления',
            'has_expiration_date' => 'Срок истечения',
            'expires_at' => 'Истекает',
        ];
    }

    /**
     * Gets query for [[Creator]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::className(), ['id' => 'creator_id']);
    }

    /**
     * Gets query for [[LabelType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLabelType()
    {
        return $this->hasOne(StatusLabelType::className(), ['id' => 'label_type_id']);
    }
}