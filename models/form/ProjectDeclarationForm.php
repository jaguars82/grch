<?php

namespace app\models\form;

use yii\base\Model;
use yii\web\UploadedFile;
use app\components\traits\ProcessFile;

/**
 * Form for loading project declaration
 */
class ProjectDeclarationForm extends Model
{
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
        
        $this->processFile('file');
        
        return true;
    }
}
