<?php

namespace app\models;

use app\models\SupportMessage;
use app\components\traits\FillAttributes;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
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
class SupportTicket extends ActiveRecord
{

    use FillAttributes;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'support_tickets';
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
    public function getMessages()
    {
        return $this->hasMany(SupportMessage::className(), ['ticket_id' => 'id']);
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
     * Gets all the active tickets
     */
    public function getAllTickets()
    {
        $tickets = $this->find()
        ->orderBy(['created_at' => SORT_ASC])
        ->all();

        return $tickets;
    }

    /**
     * Gets a ticket by ID
     */
    public function getTicketById($id)
    {
        $ticket = $this->findOne($id);
    }

    /**
     * Gets amount of tickets created by particular user
     * 
     * @return int
     */
    public function getTicketsAmountByAuthor($user_id)
    {
        // $tickets = SupportTicket::find($user_id)
        $tickets = $this->find()
        ->where([
            'author_id' => $user_id,
        ])
        ->count();

        if($tickets === null) {
            $tickets = 0;
        }

        return $tickets;
    }

    /**
     * Gets all the tickets created by particular user
     */
    public function getTicketsByAuthor($user_id) {
        $tickets = $this->find()
        ->where([
            'author_id' => $user_id,
        ])
        ->orderBy(['id' => SORT_DESC])
        ->all();
        return $tickets;
    }
}
