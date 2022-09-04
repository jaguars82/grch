<?php

namespace app\models;

use app\components\traits\FillAttributes;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "offer".
 *
 * @property int $id
 * @property int $initiator_id
 * @property string|null $type
 * @property string|null $recipient_group
 * @property int $recipient_id
 * @property string|null $topic
 * @property string|null $body
 * @property string|null $initiator_comment
 * @property string|null $action_text
 * @property string|null $action_url
 * @property boolean $seen_by_recipient
 * @property string|null $seen_by_recipient_at
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Initiator $user
 * @property Recipient $user
 */
class Notification extends ActiveRecord
{
    use FillAttributes;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notification';
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
    public function rules()
    {
        return [
            [['initiator_id'], 'required'],
            [['initiator_id', 'recepient_id'], 'integer'],
            [['new_price_cash', 'new_price_credit'], 'number'],
            [['settings'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['url'], 'string', 'max' => 200],
            [['flat_id'], 'exist', 'skipOnError' => true, 'targetClass' => Flat::className(), 'targetAttribute' => ['flat_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'flat_id' => 'Квартира',
            'new_price' => 'Новая цена',
            'url' => 'Короткая ссылка',
            'settings' => 'Настройки',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
        ];
    }
    
}