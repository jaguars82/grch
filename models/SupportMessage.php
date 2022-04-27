<?php

namespace app\models;

use app\components\traits\FillAttributes;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
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
 * @property SupportTicket $ticket
 */
class SupportMessage extends ActiveRecord
{

    use FillAttributes;

    private $authorName;
    private $authorSurname;
    private $authorAvatar;
    private $authorRole;
    private $authorAgency;

    public function setAuthorName() {
        $author = $this->author;
        $this->authorName = $author->first_name;
    }

    public function getAuthorName() {
       return $this->authorName;
    }

    public function setAuthorSurname() {
        $author = $this->author;
        $this->authorSurname = $author->last_name;
    }

    public function getAuthorSurname() {
       return $this->authorSurname;
    }

    public function setAuthorAvatar() {
        $author = $this->author;
        $this->authorAvatar = $author->photo;
    }

    public function getAuthorAvatar() {
       return $this->authorAvatar;
    }

    public function setAuthorRole() {
        $author = $this->author;
        $this->authorRole = $author->roleLabel;
    }

    public function getAuthorRole() {
       return $this->authorRole;
    }

    public function setAuthorAgency() {
        $author = $this->author;
        $this->authorAgency = $author->agency;
    }

    public function getAuthorAgency() {
       return $this->authorAgency;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'support_messages';
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
            [['ticket_id', 'author_id', 'message_number', 'author_role'], 'required'],
            [['ticket_id', 'author_id', 'message_number', 'reply_on', 'seen_by_interlocutor'], 'integer'],
            [['text'], 'string'],
            //[['created_at', 'updated_at'], 'safe'],
            //[['author_role'], 'string', 'max' => 15],
            //[['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['author_id' => 'id']],
            //[['ticket_id'], 'exist', 'skipOnError' => true, 'targetClass' => SupportTicket::className(), 'targetAttribute' => ['ticket_id' => 'id']],
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

    /**
     * Gets amount of messages in this ticket
     * 
     * @return int
     */
    public function getMessagesAmount($ticket_id)
    {
        $messages = $this->find()
        ->where([
            'ticket_id' => $ticket_id,
        ])
        ->count();

        if($messages === null) {
            $messages = 0;
        }

        return $messages;
    }    
}
