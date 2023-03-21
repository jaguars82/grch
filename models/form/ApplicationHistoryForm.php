<?php

namespace app\models\form;

use yii\base\Model;

class ApplicationHistoryForm extends Model
{
    public $application_id;
    public $user_id;
    public $action;
    public $reason;
    public $comment;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['application_id', 'action', 'user_id'], 'required'],
            [['application_id', 'action', 'user_id'], 'integer'],
            [['reason', 'comment'], 'string'],
            [['made_at'], 'safe'],
        ];
    }
}