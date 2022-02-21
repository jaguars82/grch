<?php

namespace app\models\form;

use yii\web\UploadedFile;
use app\components\traits\ProcessFile;
use app\components\traits\FillAttributes;
use app\models\Archive;
use yii\base\Model;

/**
 * Form for loading archive
 */
class ArchiveForm extends Model
{
    use FillAttributes;
    use ProcessFile;
 
    public $file;
    
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
        $this->file = UploadedFile::getInstance($this, 'file');

        if (!$this->validate()) {
            return false;
        }
        
        $this->processFile('file', Archive::FILE_PATH);
        
        return true;
    }
}
