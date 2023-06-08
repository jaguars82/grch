<?php

namespace app\models\form;

use yii\base\Model;

class CommercialForm extends Model
{
    public $initiator_id;
    public $number;
    public $name;
    public $active;
    public $is_formed;
    public $settings;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['initiator_id'], 'required'],
            [['initiator_id'], 'integer'],
            [['active', 'is_formed'], 'boolean'],
            [['settings', 'name', 'number'], 'string'],
        ];
    }
}