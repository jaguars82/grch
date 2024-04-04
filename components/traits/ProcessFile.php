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
    
    /**
     * Process incoming via Inertia files
     * 
     * @param string $fieldName
     * @param boolean $isSaveOriginName
     */
    private function processInertiaFiles($fieldName, $isSaveOriginName = false)
    {        
        if (!is_null($this->$fieldName)) {
            $files = [];
            foreach ($this->$fieldName as $file) {
                $filename = $file['baseName'] . mt_rand() . '.' . $file['extension'];
                $file->saveAs('uploads/' . $filename);
                if ($isSaveOriginName) {
                    $files[$filename] = $file['baseName'] . '.' . $file['extension'];
                } else {
                    $files[] = $filename;
                }
            }
            $this->$fieldName = $files;
        }
    }

    /**
     * Build array of file instances from incoming files
     */
    private function getInertiaFileInstances($fieldName)
    {
        $files = array();
        // fill files array with file instances from uploaded files ($_FILES)
        if (isset($_FILES[$fieldName]) && is_array($_FILES[$fieldName])) {
            foreach (array_keys($_FILES[$fieldName]['name']) as $i) { // loop over 0,1,2,3 etc...
                foreach(array_keys($_FILES[$fieldName]) as $j) { // loop over 'name', 'size', 'error', etc...
                    $files[$i][$j] = $_FILES[$fieldName][$j][$i]; // "swap" keys and copy over original array values
                }
            }
        }

        // add some fields to the array
        if (count($files) > 0) {
            foreach ($files as $ind => $file) {
                $files[$ind]['baseName'] = $file['name'];
                $files[$ind]['extension'] = pathinfo($file['name'], PATHINFO_EXTENSION);
            }
        }
        //echo '<pre>'; var_dump($files); echo '</pre>'; die;
        return $files;
    }
}