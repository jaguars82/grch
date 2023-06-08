<?php

namespace app\models\form;

use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Form for import developer's data
 */
class ImportForm extends Model
{    
    public $file;
        
    public $developer;
    public $importName;
    public $importObject;
    public $endpoint;
    public $data = NULL;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['file'], 'required'],
            [['file'], 'file', 'skipOnEmpty' => false],
        ];
    }
    
    /**
     * Process request's data
     * 
     * @return boolean
     */
    public function process()
    {
        if (!file_exists($this->importObject->algorithmPath)) {
            return false;
        }
        $import = $this->importObject->algorithmAsObject;
        
        if (is_null($this->endpoint)) {            
            $this->file = UploadedFile::getInstance($this, 'file');

            if (!$this->validate()) {
                return false;
            }
            
            if (!($this->data = $import->parseFile($this->file->tempName))) {                
                return false;
            }
        } else {            
            try {
                if (!($this->data = $import->getAndParse($this->endpoint))) {
                    return false;
                }
            } catch (\yii\httpclient\Exception $e) {
                return false;
            }
        }
        
        return true;
    }
}
