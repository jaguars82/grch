<?php

namespace app\models\form;

use yii\base\Model;

class NotificationForm extends Model
{
    public $initiator_id;
    public $type;
    public $recipient_group;
    public $recipient_id;
    public $topic;
    public $body;
    public $initiator_comment;
    public $action_text;
    public $action_url;
    public $seen_by_recipient;
    public $seen_by_recipient_at; 

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['initiator_id'], 'required'],
            [['initiator_id', 'recipient_id', 'type'], 'integer'],
            [['seen_by_recipient'], 'boolean'],
            [['recipient_group', 'topic', 'body', 'initiator_comment', 'action_text', 'action_url'], 'string'],
            [['created_at', 'updated_at', 'seen_by_recipient_at'], 'safe'],
        ];
    }
}