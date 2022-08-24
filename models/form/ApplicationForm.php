<?php

namespace app\models\form;

use yii\base\Model;

class ApplicationForm extends Model
{
    public $flat_id;
    public $applicant_id;
    public $status;
    public $client_firstname;
    public $client_lastname;
    public $client_middlename;
    public $client_phone;
    public $client_email;
    public $applicant_comment;
    public $manager_firstname;
    public $manager_lastname;
    public $manager_middlename;
    public $manager_phone;
    public $manager_email;
    public $admin_comment;
    public $is_active;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['flat_id', 'applicant_id'/*, 'status'*/], 'required'],
            [['flat_id', 'applicant_id', 'status'], 'integer'],
            [['client_firstname', 'client_lastname', 'client_middlename', 'client_phone', 'client_email',  'applicant_comment', 'manager_firstname', 'manager_lastname', 'manager_middlename', 'manager_phone', 'manager_email', 'admin_comment'], 'string'],
            [['is_active'], 'boolean'],
        ];
    }
}