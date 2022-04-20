<?php

namespace app\models\form;

use yii\base\Model;

/**
 * Form for support message.
 */
class SupportMessageForm extends Model
{
    const SCENARIO_UPDATE = 'update';

    public $ticket_id;
    public $author_id;
    public $message_number;
    public $reply_on;
    public $author_role;
    public $seen_by_interlocutor;
    public $text;
    public $created_at;
    public $updated_at;

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        $commonFields = [
            'text'
        ];
        
        return [
            self::SCENARIO_DEFAULT => $commonFields,
            self::SCENARIO_UPDATE => $commonFields,
        ];
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
            // [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['author_id' => 'id']],
            // [['ticket_id'], 'exist', 'skipOnError' => true, 'targetClass' => SupportTickets::className(), 'targetAttribute' => ['ticket_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
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
     * Process request's data
     * 
     * @return boolean
     */
    public function process()
    {
        if (!$this->validate()) {
            return false;
        }

        return true;
    }
}