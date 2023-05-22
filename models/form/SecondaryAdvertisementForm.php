<?php

namespace app\models\form;

use yii\base\Model;

class SecondaryAdvertisementForm extends Model
{
    public $external_id;
    public $deal_type;
    public $deal_status_string;
    public $agency_id;
    public $creation_type;
    public $author_id;
    public $author_info;
    public $is_active;
    public $is_moderated;
    public $is_moderation_ok;
    public $moderator_id;
    public $moderated_at;
    public $expiration_date;
    public $is_expired;
    public $is_prolongated;
    public $last_prolongation_date;
    public $creation_date;
    public $last_update_date;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['agency_id', 'creation_type', ''], 'required'],
            [['deal_type', 'creation_type', 'author_id', 'moderator_id'], 'integer'],
            [['external_id', 'deal_status_string', 'agency_id', 'author_info', 'moderated_at', 'moderator_comment', 'expiration_date',  'last_prolongation_date', 'creation_date', 'last_update_date'], 'string'],
            [['is_active', 'is_moderated', 'is_moderation_ok', 'is_expired', 'is_prolongated' ], 'boolean'],
        ];
    }
}