<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "support_messages".
 *
 * @property int $id
 * @property int $ticket_id
 * @property int $author_id
 * @property int $message_number
 * @property int|null $reply_on
 * @property string $author_role
 * @property int|null $seen_by_interlocutor
 * @property string|null $text
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property User $author
 * @property SupportTickets $ticket
 */
class SupportMessages extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'support_messages';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ticket_id', 'author_id', 'message_number', 'author_role'], 'required'],
            [['ticket_id', 'author_id', 'message_number', 'reply_on', 'seen_by_interlocutor'], 'integer'],
            [['text'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['author_role'], 'string', 'max' => 15],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['author_id' => 'id']],
            [['ticket_id'], 'exist', 'skipOnError' => true, 'targetClass' => SupportTickets::className(), 'targetAttribute' => ['ticket_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ticket_id' => 'ID запроса',
            'author_id' => 'ID автора',
            'message_number' => 'Номер сообщения',
            'reply_on' => 'Ответ на',
            'author_role' => 'Статус автора',
            'seen_by_interlocutor' => 'Прочитано собеседником',
            'text' => 'Текст сообщения',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }

    /**
     * Gets query for [[Author]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'author_id']);
    }

    /**
     * Gets query for [[Ticket]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTicket()
    {
        return $this->hasOne(SupportTickets::className(), ['id' => 'ticket_id']);
    }
}
