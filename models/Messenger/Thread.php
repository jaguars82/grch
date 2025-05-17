<?php

namespace app\models\Messenger;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%thread}}".
 *
 * @property int $id
 * @property int $chat_id
 * @property int $creator_id
 * @property string|null $icon
 * @property string $title
 * @property string|null $details
 * @property bool $was_edited
 * @property bool $is_archived
 * @property bool $is_deleted
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Chat $chat
 * @property Message[] $messages
 */
class Thread extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%thread}}';
    }

    public static function getDb()
    {
        return Yii::$app->get('messenger_db');
    }

    public function rules()
    {
        return [
            [['chat_id', 'creator_id', 'title'], 'required'],
            [['chat_id', 'creator_id'], 'integer'],
            [['details'], 'string'],
            [['icon', 'title'], 'string', 'max' => 255],
            [['was_edited', 'is_archived', 'is_deleted'], 'boolean'],
            [['created_at', 'updated_at'], 'safe'],
            [['chat_id'], 'exist', 'skipOnError' => true, 'targetClass' => Chat::class, 'targetAttribute' => ['chat_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'chat_id' => 'Chat ID',
            'creator_id' => 'Creator ID',
            'icon' => 'Icon',
            'title' => 'Title',
            'details' => 'Details',
            'was_edited' => 'Was Edited',
            'is_archived' => 'Is Archived',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getChat()
    {
        return $this->hasOne(Chat::class, ['id' => 'chat_id']);
    }

    public function getMessages()
    {
        return $this->hasMany(Message::class, ['thread_id' => 'id']);
    }
}
