<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "support_tickets".
 *
 * @property int $id
 * @property int $author_id
 * @property string $ticket_number
 * @property string|null $title
 * @property int|null $is_closed
 * @property int|null $has_unread_messages_from_support
 * @property int|null $has_unread_messages_from_author
 * @property string|null $last_enter_by_support
 * @property string|null $last_enter_by_author
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int|null $is_archived
 *
 * @property SupportMessages[] $supportMessages
 * @property User $author
 */
class SupportTickets extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'support_tickets';
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
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['author_id' => 'id']],
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
     * Gets query for [[SupportMessages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSupportMessages()
    {
        return $this->hasMany(SupportMessages::className(), ['ticket_id' => 'id']);
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
}
