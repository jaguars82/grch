<?php

namespace app\models\Messenger;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%message_attachment}}".
 *
 * @property int $id
 * @property int $message_id
 * @property string $type
 * @property string $location_type
 * @property string|null $url
 * @property string|null $filename
 * @property string|null $file_mime
 * @property string|null $file_ext
 * @property int|null $file_size
 * @property string $uploaded_at
 *
 * @property Message $message
 */
class MessageAttachment extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%message_attachment}}';
    }

    public static function getDb()
    {
        return Yii::$app->get('messenger_db');
    }

    public function rules()
    {
        return [
            [['message_id', 'type', 'location_type', 'uploaded_at'], 'required'],
            [['message_id', 'file_size'], 'integer'],
            [['type', 'location_type'], 'string', 'max' => 20],
            [['type'], 'in', 'range' => ['image', 'video', 'audio', 'document', 'file']],
            [['location_type'], 'in', 'range' => ['local', 'remote']],
            [['url', 'filename', 'file_mime', 'file_ext'], 'string', 'max' => 255],
            [['uploaded_at'], 'safe'],
            [['message_id'], 'exist', 'skipOnError' => true, 'targetClass' => Message::class, 'targetAttribute' => ['message_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'message_id' => 'Message ID',
            'type' => 'Type',
            'location_type' => 'Location Type',
            'url' => 'URL',
            'filename' => 'Filename',
            'file_mime' => 'File MIME',
            'file_ext' => 'File Extension',
            'file_size' => 'File Size',
            'uploaded_at' => 'Uploaded At',
        ];
    }

    public function getMessage()
    {
        return $this->hasOne(Message::class, ['id' => 'message_id']);
    }
}
