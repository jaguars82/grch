<?php

namespace app\components\archive\interfaces;

/**
 * Interface for archive services
 */
interface ArchiveInterface 
{
    /**
     * Extract archive to path
     * @param string $filePath path to filePath
     * @return boolean $success
     */
    public function extract($filePath);
    
    /**
     * Return tree list of files iin extracted path
     * @return array $treeList tree list of files
     */
    public function getTreeListFiles();
}
