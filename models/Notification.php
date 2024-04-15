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

    /** Type of notification (individual, group) */
    const TYPE_INDIVIDUAL = 1;
    const TYPE_GROUP = 2;

    /** Chain "sender -> recipient" */
    const ADMIN_TO_USERS = 1;
    const ADMIN_TO_USER = 2;
    const ADMIN_TO_DEVELOPER = 3;
    const ADMIN_TO_DEVELOPERS = 4;
    const USER_TO_ADMINS = 5;
    const DEVELOPER_TO_ADMINS = 6;
    const DEVELOPER_TO_USER = 7;

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
            [['initiator_id', 'recipient_id', 'type'], 'integer'],
            [['seen_by_recipient'], 'boolean'],
            [['recipient_group', 'topic', 'body', 'initiator_comment', 'action_text', 'action_url'], 'string'],
            [['created_at', 'updated_at', 'seen_by_recipient_at'], 'safe'],
            [['initiator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['initiator_id' => 'id']],
            [['recipient_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['recipient_id' => 'id']],
        ];
    }

    public function getNotificationsForAdmin ()
    {
        return self::find()
            ->where(['recipient_group' => 'admin'])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
    }

    public function getNotificationsForUser($userId)
    {
        return $this->find()
            ->where(['recipient_id' => $userId])
            ->orderBy(['created_at' => SORT_DESC]);
    }

    public function getUnreadNotificationsAmountForAdmin ()
    {
        return self::find()
            ->where(['recipient_group' => 'admin'])
            ->andWhere(['seen_by_recipient' => 0])
            ->count();
    }

    public function getUnreadNotificationsAmountForUser($userId)
    {
        return $this->find()
            ->where(['recipient_id' => $userId])
            ->andWhere(['seen_by_recipient' => 0])
            ->count();
    }



    
}