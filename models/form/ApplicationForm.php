<?php

namespace app\models\form;

use yii\base\Model;
use yii\web\UploadedFile;
use app\components\traits\ProcessFile;

class ApplicationForm extends Model
{
    use ProcessFile;

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
    public $recieptFile = [];
    public $recieptFilesToSave = [];
    public $is_toll;
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
            [['client_firstname', 'client_lastname', 'client_middlename', 'client_phone', 'client_email',  'applicant_comment', 'manager_firstname', 'manager_lastname', 'manager_middlename', 'manager_phone', 'manager_email', 'reservation_conditions', 'admin_comment', 'ddu_cash_paydate', 'ddu_mortgage_paydate', 'ddu_matcap_paydate', 'application_number', 'deal_success_docs'], 'string'],
            [['is_active', 'is_toll', 'receipt_provided', 'ddu_provided', 'self_reservation'], 'boolean'],
            [['recieptFile'],  'file', 'skipOnEmpty' => true, 'extensions' => 'doc, docx, pdf, txt, xls, xlsx, rtf, ppt, pptx, png, jpg, gif, jpeg,', 'maxFiles' => 100],
        ];
    }

    /**
     * Process request's data
     * 
     * @return boolean
     */
    public function processRecieptFile()
    {
        $this->recieptFile = $this->getInertiaFileInstances('recieptFile');
        $this->recieptFilesToSave = UploadedFile::getInstancesByName('recieptFile');
        
        $this->processFiles('recieptFilesToSave');

        return true;
    }
}