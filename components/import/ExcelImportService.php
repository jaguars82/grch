<?php

namespace app\components\import;

/**
 * Common parts of excel import services
 */
abstract class ExcelImportService implements ImportServiceInterface
{
    public $newbuildingComplexes = [];
    public $newbuildings = [];
    public $flats = [];
    
    /**
     * Current file spreadsheet
     *
     * @var \PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    protected $sheet;
    
    /**
     * {@inheritdoc}
     */
    public function getAndParse($endpoint)
    {
        $filename = tempnam('/tmp', '');
        $fh = fopen($filename, 'w');
        
        $client = new \yii\httpclient\Client([
            'transport' => 'yii\httpclient\CurlTransport'
        ]);
        
        $client->createRequest()
            ->setMethod('GET')
            ->setUrl($endpoint)
            ->setOutputFile($fh)
            ->send();
        
        return $this->parseFile($filename);
    }
    
    /**
     * Check that cell value is valid
     * 
     * @param type $pattern
     * @param type $match
     * @param type $col
     * @param type $row
     * @param type $data
     * @param type $exceptionMsg
     * @return string
     * @throws AppException
     */
    protected function checkValue($pattern, $match, $col, $row, $data, $exceptionMsg)
    {
        preg_match($pattern, $data[$row][$col], $matches);
        if (!isset($matches[$match])) {
            $cellCoords = $this->getCellCoords($col, $row);
            throw new AppException($exceptionMsg . " (Ячейка {$cellCoords})");
        }
        
        return $matches[$match];
    }

    /**
     * Get cell coordinates by column and row
     *
     * @param int $flatCol Column of cell
     * @param int $flatRow Row of cell
     * @return string
     */
    protected function getCellCoords($flatCol, $flatRow)
    {
        if ($flatCol < 26) {
            $flatColValue = chr(ord('A') + $flatCol);
        } else {
            $firstLetter = chr(ord('A') + $flatCol / 26 - 1);
            $secondLetter = chr(ord('A') + $flatCol % 26);
            $flatColValue = "{$firstLetter}{$secondLetter}";
        }
        
        return $flatColValue . ($flatRow + 1);
    }
    
    /**
     * Get flat status from cell
     *
     * @param int $flatCol Column of cell
     * @param int $flatRow Row of cell
     * @return boolean
     */
    protected function getStatus($flatCol, $flatRow)
    {
        $color = $this->sheet->getStyle($this->getCellCoords($flatCol, $flatRow))->getFill()->getStartColor()->getRGB();
        
        return (isset($this->status[$color])) ? $this->status[$color] : -1;
    }
}
