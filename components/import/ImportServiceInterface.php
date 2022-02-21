<?php

namespace app\components\import;

/**
 * Interface for import service
 */
interface ImportServiceInterface 
{
    const SUPPORT_AUTO = 'isSupportAuto';
    const SUPPORT_MANUAL = 'isSupportManual';
    
    /**
     * Getting and parsing file with flat's data
     * 
     * @param string $endpoint
     * @return array
     */
    public function getAndParse($endpoint);
    
    /*
     * Check that service support auto import
     * 
     * @return boolean
     */
    public function isSupportAuto();
    
    /*
     * Check that service support manual import
     * 
     * @return boolean
     */
    public function isSupportManual();
    
    /**
     * Process file to extract flat's data
     * 
     * @param string $filename
     * @return boolean
     */
    public function parseFile($filename);
}
