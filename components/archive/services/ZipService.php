<?php

namespace app\components\archive\services;

use app\components\archive\interfaces\ArchiveInterface;
use app\components\archive\BaseService;
use yii\base\InvalidArgumentException;

/**
 * Service for extract archive
 */
class ZipService extends BaseService implements ArchiveInterface
{
    public function extract($filePath)
    {
        if(!file_exists($filePath) || self::getExtension($filePath) != 'zip') {
            throw new InvalidArgumentException('File not found');
        }

        $archive = new \ZipArchive;
        $this->extractPath = $this->tempPath . self::getFileName($filePath);
        
        if ($archive->open($filePath)) {
            for($i = 0; $i < $archive->numFiles; ++$i) {
                $name = $archive->getNameIndex($i, 64);
                $archive->renameIndex($i, mb_convert_encoding($name, 'utf-8', 'utf-8, cp866'));
            }
            
            $archive->close();

            if($archive->open($filePath)) {
                $archive->extractTo($this->extractPath);
                $archive->close();
            }

            $this->isExtracted = true;
        } else {
            $this->isExtracted = false;
        }

        return $this->isExtracted;
    }
}