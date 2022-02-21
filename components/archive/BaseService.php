<?php

namespace app\components\archive;

/**
 * Base service for extracting archive
 */
class BaseService
{
    public $tempPath;
    public $extractPath;

    public function __construct($tempPath)
    {
        $this->tempPath = $tempPath;
    }
    
    /**
     * Return file name from path
     * @param string $filePath path ro file
     * @return string $fileName file name
     */
    public static function getFileName($filePath)
    {
        $explodePath = explode('/', $filePath); //DIRECTORY_SEPARATOR
        $name = $explodePath[count($explodePath) - 1];
        
        return substr($name, 0, strrpos($name, '.'));
    }

    /**
     * Return extension file from name
     * @param string $name file name
     * @return string $extension file extension
     */
    public static function getExtension($name)
    {
        $dotIndex = strrpos($name, '.');

        if($dotIndex === false) {
            return false;
        }

        return substr($name, $dotIndex + 1);
    }

    /**
     * Return tree list of files iin extracted path
     * @return array $treeList tree list of files
     */
    public function getTreeListFiles()
    {
        if(!is_dir($this->extractPath)) {
            return false;
        }

        return $this->treeListFiles($this->extractPath);
    }

    /**
     * Recursive return tree list
     * @param $directory directory
     * @return array $treeList tree list of files
     */
    private function treeListFiles($directory)
    {
        $files = scandir($directory);
        sort($files, SORT_NUMERIC);

        unset($files[array_search('.', $files, true)]);
        unset($files[array_search('..', $files, true)]);

        if (count($files) < 1)
            return;

        $result = [];

        foreach($files as $file) {
            if(is_dir($directory . DIRECTORY_SEPARATOR . $file)) {
                $result[$file] = $this->treeListFiles($directory . DIRECTORY_SEPARATOR . $file);
            } else {
                $result[] = $file;
            }
        }

        return $result;
    }

    /**
     * Remove extracted directory with all files and subdirectories
     * @return boolean $success
     */
    public function clean()
    {
        if(!$this->extractPath || !file_exists($this->extractPath)) {
            return false;
        }

        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->extractPath, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST);

        foreach ( $iterator as $file ) {
            $file->isDir() ?  rmdir($file) : unlink($file);
        }
        rmdir($this->extractPath);

        return true;
    }
}