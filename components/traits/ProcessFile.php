<?php

namespace app\components\traits;

/**
 * Trait adding functions for process loaded file/files
 */
trait ProcessFile 
{
    /**
     * Process incoming file
     * 
     * @param string $fieldName
     */
    private function processFile($fieldName, $newPath = '')
    {        
        if (!is_null($this->$fieldName)) {
            $fileName = $this->$fieldName->baseName . mt_rand() . '.' . $this->$fieldName->extension;
            if($newPath) {
                $path = \Yii::getAlias($newPath . $fileName);
            } else {
                $path = \Yii::getAlias("@webroot/uploads/$fileName");
            }
            $this->$fieldName->saveAs($path);
            $this->$fieldName = $fileName;
        }
    }
    
    /**
     * Process incoming files
     * 
     * @param string $fieldName
     * @param boolean $isSaveOriginName
     */
    private function processFiles($fieldName, $isSaveOriginName = false)
    {        
        if (!is_null($this->$fieldName)) {
            $files = [];
            foreach ($this->$fieldName as $file) {
                $filename = $file->baseName . mt_rand() . '.' . $file->extension;
                $file->saveAs('uploads/' . $filename);
                if ($isSaveOriginName) {
                    $files[$filename] = $file->baseName . '.' . $file->extension;
                } else {
                    $files[] = $filename;
                }
            }
            $this->$fieldName = $files;
        }
    }
}