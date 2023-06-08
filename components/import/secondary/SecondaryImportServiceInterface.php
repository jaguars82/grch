<?php

namespace app\components\import\secondary;

/**
 * Interface for import service
 */
interface SecondaryImportServiceInterface 
{
    /**
     * Getting and parsing file with data
     * 
     * @param string $endpoint
     * @return array
     */
    public function getAndParse($endpoint);
}