<?php

namespace app\models\form;

use yii\base\Model;

class ApplicationForm extends Model
{
    public $flat_id;
    public $developer_id;
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
    public $reservation_conditions;
    public $admin_comment;
    public $self_reservation;
    public $is_active;
    public $application_number;
    public $deal_success_docs;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['flat_id', 'applicant_id', 'developer_id'/*, 'status'*/], 'required'],
            [['flat_id', 'developer_id', 'applicant_id', 'status'], 'integer'],
            [['client_firstname', 'client_lastname', 'client_middlename', 'client_phone', 'client_email',  'applicant_comment', 'manager_firstname', 'manager_lastname', 'manager_middlename', 'manager_phone', 'manager_email', 'reservation_conditions', 'admin_comment', 'application_number', 'deal_success_docs'], 'string'],
            [['is_active', 'self_reservation'], 'boolean'],
        ];
    }
}