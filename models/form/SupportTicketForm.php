<?php

namespace app\models\form;

use yii\base\Model;

/**
 * Form for support tickets
 */
class SupportTicketForm extends Model
{
    const SCENARIO_UPDATE = 'update';

    // public $id;
    public $author_id;
    public $ticket_number;
    public $title;
    public $is_closed;
    public $has_unread_messages_from_support;
    public $has_unread_messages_from_author;
    public $last_enter_by_support;
    public $last_enter_by_author;
    public $created_at;
    public $updated_at;
    public $is_archived;

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        $commonFields = [
            'author_id', 'ticket_number', 'title', 'is_closed'
        ];
        
        return [
            self::SCENARIO_DEFAULT => $commonFields,
            self::SCENARIO_UPDATE => array_merge($commonFields, ['is_archived']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['author_id', 'ticket_number'], 'required'],
            [['author_id', 'is_closed', 'has_unread_messages_from_support', 'has_unread_messages_from_author', 'is_archived'], 'integer'],
            [['last_enter_by_support', 'last_enter_by_author', 'created_at', 'updated_at'], 'safe'],
            [['ticket_number'], 'string', 'max' => 30],
            [['title'], 'string', 'max' => 255],
            [['ticket_number'], 'unique'],
            // [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['author_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'author_id' => 'ID автора',
            'ticket_number' => 'Номер запроса',
            'title' => 'Тема',
            'is_closed' => 'Закрыт',
            'has_unread_messages_from_support' => 'Есть непрочтенные сообщения от поддержки',
            'has_unread_messages_from_author' => 'Есть непрочтенные сообщения от автора',
            'last_enter_by_support' => 'Время последнего просмотра поддержкой',
            'last_enter_by_author' => 'Время последнего просмотра автором',
            'created_at' => 'Создан',
            'updated_at' => 'Обновлен',
            'is_archived' => 'В архиве',
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
