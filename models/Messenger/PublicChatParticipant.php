<?php

namespace app\models\Messenger;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%public_chat_participant}}".
 *
 * @property int $id
 * @property int $chat_id
 * @property int $user_id
 * @property string|null $icon
 * @property string $joined_at
 * @property string|null $left_at
 *
 * @property Chat $chat
 */
class PublicChatParticipant extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%public_chat_participant}}';
    }

    public static function getDb()
    {
        return Yii::$app->get('messenger_db');
    }

    public function rules()
    {
        return [
            [['chat_id', 'user_id', 'joined_at'], 'required'],
            [['chat_id', 'user_id'], 'integer'],
            [['icon'], 'string', 'max' => 255],
            [['joined_at', 'left_at'], 'safe'],
            [['chat_id'], 'exist', 'skipOnError' => true, 'targetClass' => Chat::class, 'targetAttribute' => ['chat_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'chat_id' => 'Chat ID',
            'user_id' => 'User ID',
            'icon' => 'Icon',
            'joined_at' => 'Joined At',
            'left_at' => 'Left At',
        ];
    }

    public function getChat()
    {
        return $this->hasOne(Chat::class, ['id' => 'chat_id']);
    }
}