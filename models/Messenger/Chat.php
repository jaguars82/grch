<?php

namespace app\models\Messenger;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%chat}}".
 *
 * @property int $id
 * @property int $creator_id
 * @property int|null $interlocuter_id
 * @property string|null $icon
 * @property string|null $title
 * @property string|null $details
 * @property string $type
 * @property bool $is_url_attached
 * @property string|null $url
 * @property bool $was_edited
 * @property bool $is_archived
 * @property bool $is_deleted
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Thread[] $threads
 * @property Message[] $messages
 * @property PublicChatParticipant[] $publicChatParticipants
 */
class Chat extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%chat}}';
    }

    public static function getDb()
    {
        return Yii::$app->get('messenger_db');
    }

    public function rules()
    {
        return [
            [['creator_id', 'type'], 'required'],
            [['creator_id', 'interlocuter_id'], 'integer'],
            [['details'], 'string'],
            [['type'], 'string', 'max' => 20],
            [['type'], 'in', 'range' => ['private', 'public']],
            [['icon', 'title', 'url'], 'string', 'max' => 255],
            [['is_url_attached', 'was_edited', 'is_archived', 'is_deleted'], 'boolean'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'creator_id' => 'Creator ID',
            'interlocuter_id' => 'Interlocuter ID',
            'icon' => 'Icon',
            'title' => 'Title',
            'details' => 'Details',
            'type' => 'Type',
            'is_url_attached' => 'Is URL Attached',
            'url' => 'URL',
            'was_edited' => 'Was Edited',
            'is_archived' => 'Is Archived',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getThreads()
    {
        return $this->hasMany(Thread::class, ['chat_id' => 'id']);
    }

    public function getMessages()
    {
        return $this->hasMany(Message::class, ['chat_id' => 'id']);
    }

    public function getPublicChatParticipants()
    {
        return $this->hasMany(PublicChatParticipant::class, ['chat_id' => 'id']);
    }
}
