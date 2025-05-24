<?php

namespace app\models\Messenger;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%message}}".
 *
 * @property int $id
 * @property int $chat_id
 * @property int|null $thread_id
 * @property int $author_id
 * @property string|null $text
 * @property int|null $reply_on_id
 * @property bool $is_seen_by_interlocutor
 * @property bool $was_edited
 * @property bool $is_archived
 * @property bool $is_deleted
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Chat $chat
 * @property Thread $thread
 * @property Message $replyOn
 * @property Message[] $replies
 * @property MessageAttachment[] $attachments
 */
class Message extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%message}}';
    }

    public static function getDb()
    {
        return Yii::$app->get('messenger_db');
    }

    public function rules()
    {
        return [
            [['chat_id', 'author_id'], 'required'],
            [['chat_id', 'thread_id', 'author_id', 'reply_on_id'], 'integer'],
            [['text'], 'string'],
            [['is_seen_by_interlocutor', 'was_edited', 'is_archived', 'is_deleted'], 'boolean'],
            [['created_at', 'updated_at'], 'safe'],
            [['chat_id'], 'exist', 'skipOnError' => true, 'targetClass' => Chat::class, 'targetAttribute' => ['chat_id' => 'id']],
            [['thread_id'], 'exist', 'skipOnError' => true, 'targetClass' => Thread::class, 'targetAttribute' => ['thread_id' => 'id']],
            [['reply_on_id'], 'exist', 'skipOnError' => true, 'targetClass' => Message::class, 'targetAttribute' => ['reply_on_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'chat_id' => 'Chat ID',
            'thread_id' => 'Thread ID',
            'author_id' => 'Author ID',
            'text' => 'Text',
            'reply_on_id' => 'Reply On ID',
            'is_seen_by_interlocutor' => 'Is Seen By Interlocutor',
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

    public function getThread()
    {
        return $this->hasOne(Thread::class, ['id' => 'thread_id']);
    }

    public function getReplyOn()
    {
        return $this->hasOne(Message::class, ['id' => 'reply_on_id']);
    }

    public function getReplies()
    {
        return $this->hasMany(Message::class, ['reply_on_id' => 'id']);
    }

    public function getAttachments()
    {
        return $this->hasMany(MessageAttachment::class, ['message_id' => 'id']);
    }
}
