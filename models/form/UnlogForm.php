<?php

namespace app\models\form;

use app\models\User;
use Yii;
use yii\base\Model;

class UnlogForm extends Model
{
    public $email;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['email'], 'required'],
        ];
    }
}