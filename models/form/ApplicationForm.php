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
    public $is_toll;
    public $recieptFile = [];
    public $recieptFilesToSave = [];
    public $agentDocpack = [];
    public $agentDocpackToSave = [];
    public $developerDocpack = [];
    public $developerDocpackToSave = [];
    public $ddu_price;
    public $ddu_cash;
    public $ddu_mortgage;
    public $ddu_matcap;
    public $ddu_cash_paydate;
    public $ddu_mortgage_paydate;
    public $ddu_matcap_paydate;
    public $dduFile = [];
    public $dduFilesToSave = [];
    public $reportActFile = [];
    public $reportActToSave = [];
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
            [['ddu_price', 'ddu_cash', 'ddu_mortgage', 'ddu_matcap'], 'double'],
            [['client_firstname', 'client_lastname', 'client_middlename', 'client_phone', 'client_email',  'applicant_comment', 'manager_firstname', 'manager_lastname', 'manager_middlename', 'manager_phone', 'manager_email', 'reservation_conditions', 'admin_comment', 'ddu_cash_paydate', 'ddu_mortgage_paydate', 'ddu_matcap_paydate', 'application_number', 'deal_success_docs'], 'string'],
            [['is_active', 'is_toll', 'receipt_provided', 'ddu_provided', 'self_reservation'], 'boolean'],
            [['recieptFile'],  'file', 'skipOnEmpty' => true, 'extensions' => 'doc, docx, pdf, txt, xls, xlsx, rtf, ppt, pptx, png, jpg, gif, jpeg,', 'maxFiles' => 100],
            [['dduFile'],  'file', 'skipOnEmpty' => true, 'extensions' => 'doc, docx, pdf, txt, xls, xlsx, rtf, ppt, pptx, png, jpg, gif, jpeg,', 'maxFiles' => 100],
            [['agentDocpack'],  'file', 'skipOnEmpty' => true, 'extensions' => 'doc, docx, pdf, txt, xls, xlsx, rtf, ppt, pptx, png, jpg, gif, jpeg,', 'maxFiles' => 100],
            [['developerDocpack'],  'file', 'skipOnEmpty' => true, 'extensions' => 'doc, docx, pdf, txt, xls, xlsx, rtf, ppt, pptx, png, jpg, gif, jpeg,', 'maxFiles' => 100],
            [['reportActFile'],  'file', 'skipOnEmpty' => true, 'extensions' => 'doc, docx, pdf, txt, xls, xlsx, rtf, ppt, pptx, png, jpg, gif, jpeg,', 'maxFiles' => 100],
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

    public function processAgentDocpackFile()
    {
        $this->agentDocpack = $this->getInertiaFileInstances('agentDocpack');
        $this->agentDocpackToSave = UploadedFile::getInstancesByName('agentDocpack');
        
        $this->processFiles('agentDocpackToSave');

        return true;
    }

    public function processDeveloperDocpackFile()
    {
        $this->developerDocpack = $this->getInertiaFileInstances('developerDocpack');
        $this->developerDocpackToSave = UploadedFile::getInstancesByName('developerDocpack');
        
        $this->processFiles('developerDocpackToSave');

        return true;
    }

    public function processDduFile()
    {
        $this->dduFile = $this->getInertiaFileInstances('dduFile');
        $this->dduFilesToSave = UploadedFile::getInstancesByName('dduFile');
        
        $this->processFiles('dduFilesToSave');

        return true;
    }

    public function processReportActFile()
    {
        $this->reportActFile = $this->getInertiaFileInstances('reportActFile');
        $this->reportActToSave = UploadedFile::getInstancesByName('reportActFile');
        
        $this->processFiles('reportActToSave');

        return true;
    }
}