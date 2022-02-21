<?php

namespace app\components\import\Vibor;

use app\components\exceptions\AppException;
use app\components\import\ExcelImportService as Service;
use app\models\Flat;

/**
 * Service for importing flat's data from excel file for developer "Выбор"
 */
class ExcelImportService extends Service
{
    /**
     * Status value for cell colors
     * 
     * @var integer 
     */
    protected $status = [
        'FFFFFF' => Flat::STATUS_SALE,
        'C0C0C0' => Flat::STATUS_SOLD,
        'FFFF99' => Flat::STATUS_RESERVED,
    ];
    
    /**
     * {@inheritdoc}
     */
    public function isSupportAuto()
    {
        return false;
    }
    
    /**
     * {@inheritdoc}
     */
    public function isSupportManual()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function parseFile($filename)
    {        
        $sheets = \PhpOffice\PhpSpreadsheet\IOFactory::load($filename);
        
        for ($i = 0; $i < $sheets->getSheetCount(); $i++)
        {
            $this->sheet = $sheets->getSheet($i);
            $sheetData = $this->sheet->toArray(null, true, true, false);
            $maxRow = count($sheetData);
            $maxCol = count($sheetData[0]);
            
            $this->findFlatsData($sheetData, $maxRow, $maxCol);
        }
        
        $this->flats = $this->processCompositeFlats($this->flats);
        
        return [
            'newbuildingComplexes' => $this->newbuildingComplexes,
            'newbuildings' => $this->newbuildings,
            'flats' => $this->flats,
        ];
    }
    
    /**
     * Find flats data
     * 
     * @param type $sheetData All data from sheet as array
     * @throws AppException
     */
    private function findFlatsData($sheetData, $maxRow, $maxCol)
    {        
        for ($row = 1; $row < $maxRow; $row++) {
            for ($col = 0; $col < $maxCol; $col++) {
                if (preg_match("/^Объект недвижимости: (?<data>(.*?)). Площадь/", $sheetData[$row][$col], $matches)) {
                    
                    if (($pos = strpos($matches['data'], 'позиция')) === false) {
                        $newbuildingName = 'Позиция';
                        $newbuildingAddress = $matches['data'];
                    } else {
                        $newbuildingName = trim(substr($matches['data'], $pos));
                        $newbuildingAddress = trim(substr($matches['data'], 0, strlen($matches['data']) - strlen($newbuildingName)), " ,");
                    }
       
                    if (($currentNewbuildingComplexId = $this->getCurrentEntityId($this->newbuildingComplexes, $newbuildingAddress)) == -1) {
                        $this->newbuildingComplexes[] = [
                            'name' => $newbuildingAddress,
                            'address' => $newbuildingAddress,
                        ];
                        $currentNewbuildingComplexId = count($this->newbuildingComplexes) - 1;
                    }
                    
                    break 2;
                }
            }
        }
        
        if (($currentNewbuildingId = $this->getCurrentEntityId($this->newbuildings, $newbuildingName)) == -1) {
            $this->newbuildings[] = [
                'objectId' => $currentNewbuildingComplexId,
                'name' => $newbuildingName,
                'address' => $newbuildingAddress,
            ];
            $currentNewbuildingId = count($this->newbuildings) - 1;
        }
        
        $flats = [];
        
        for ($row = 1; $row < $maxRow; $row++) {
            for ($col = 0; $col < $maxCol; $col++) {
                if (preg_match("/^Этаж$/", $sheetData[$row][$col])) {                    
                    $section = (int)$this->checkValue("/Подъезд №(?<section>\d+)/", 'section', $col + 1, $row, $sheetData, "Нет данных о подъезде");
                    $floors = (int)$this->checkValue("/Этаж (?<floor>\d+)/", 'floor', $col, $row + 1, $sheetData, "Нет данных об этаже");
                    
                    $this->newbuildings[$currentNewbuildingId]['total_floor'] = (isset($this->newbuildings[$currentNewbuildingId]['total_floor']))
                        ? max($this->newbuildings[$currentNewbuildingId]['total_floor'], $floors)
                        : $floors;

                    for ($sectionRow = $row + 1; $sectionRow < $floors * 6 + ($row + 1); $sectionRow += 6) {
                        preg_match("/Этаж (?<floor>\d+)/", $sheetData[$sectionRow][$col], $matches);
                        if (!isset($matches['floor'])) {
                            if (empty($sheetData[$sectionRow][$col + 1])) {
                                break;
                            } else {
                                $cellCoords = $this->getCellCoords($col, $sectionRow);
                                throw new AppException("Нет данных об этаже (Ячейка {$cellCoords})");
                            }                            
                        }
                        $floor = (int)$matches['floor'];
                        
                        for ($sectionCol = $col + 1; $sectionCol < $maxCol; $sectionCol += 3) {
                            if(preg_match("/^Этаж/", $sheetData[$sectionRow][$sectionCol])) {
                                break;
                            }
                            
                            $number = (int)$this->checkValue("/^(?<number>\d+)$/", 'number', $sectionCol, $sectionRow, $sheetData, "Нет данных об номере квартиры");
                            $rooms = (int)$this->checkValue("/^(?<rooms>\d+)$/", 'rooms', $sectionCol + 2, $sectionRow + 2, $sheetData, "Нет данных о количестве комнат в квартире");
                            $area = $this->checkValue("/^(?<area>[\d,]+)$/", 'area', $sectionCol + 2, $sectionRow + 3, $sheetData, "Нет данных о площади квартиры");
                            $area = (float)str_replace(',', '.', $area);
                            
                            $status = (int)$this->getStatus($sectionCol, $sectionRow);
                            if ($status == -1) {
                                $cellCoords = $this->getCellCoords($sectionCol, $sectionRow);
                                throw new AppException("Неизвестный статус квартиры (Ячейка {$cellCoords})");
                            }
                            
                            if ($status === 0) {
                                $unit_price_cash = $this->checkValue("/Нал: (?<unit_price>[\d ,]+)/", 'unit_price', $sectionCol, $sectionRow + 4, $sheetData, "Нет данных о наличной стоимости квартиры");
                                $unit_price_cash = (float)str_replace(' ', '', $unit_price_cash);
                                $price_cash = round($unit_price_cash * $area);
                                
                                $unit_price_credit = $this->checkValue("/Ипотека: (?<unit_price>[\d ,]+)/", 'unit_price', $sectionCol, $sectionRow + 4, $sheetData, "Нет данных об ипотечной стоимости квартиры");
                                $unit_price_credit = (float)str_replace(' ', '', $unit_price_credit);
                                $price_credit = round($unit_price_credit * $area);
                            } else {
                                $unit_price_cash = NULL;
                                $price_cash = NULL;
                                
                                $unit_price_credit = NULL;
                                $price_credit = NULL;
                            }
                            
                            $this->flats[] = [
                                'houseId' => $currentNewbuildingId,
                                'section' => (int)$section,
                                'floor' => (int)$floor,
                                'number' => (int)$number,
                                'rooms' => (int)$rooms,
                                'area' => $area,                                
                                'unit_price_cash' => $unit_price_cash,
                                'price_cash' => $price_cash,
                                'unit_price_credit' => $unit_price_credit,
                                'price_credit' => $price_credit,
                                
                                'status' => (int)$status,
                            ];
                        }
                    }
                    
                    $row += $floors * 6;
                    
                    try {
                        $sheetData[$row][$col];
                    } catch (\Exception $ex) {
                        break;
                    }
                }
            }
        }
    }
    
    /**
     * Get current entity ID
     * 
     * @param array $entities
     * @param string $entityName
     * @return int
     */
    private function getCurrentEntityId($entities, $entityName)
    {
        $currentEntityId = -1;
            
        foreach ($entities as $key => $entity) {
            if ($entity['name'] == $entityName) {
                $currentEntityId = $key;
                break;
            }
        }
        
        return $currentEntityId;
    }
    
    /**
     * Process composite flats
     * 
     * @param array $flats
     * @return array
     */
    private function processCompositeFlats($flats)
    {
        $flatsArray = [];
        $complexObjectId = 0;
        
        foreach ($flats as $key => $flat) {
            $flatsArray[$flat['houseId']][$flat['number']][] = $key;
        }
        
        foreach ($flatsArray as $houseId => $flatsItem) {
            $flatsArray[$houseId] = array_filter($flatsArray[$houseId], function($flats){
                return count($flats) > 1;
            });
        }

        foreach ($flatsArray as $houseItem) {
            foreach ($houseItem as $flatsItem) {
                foreach ($flatsItem as $flatId) {
                    $flats[(int)$flatId]['compositeFlatId'] = $complexObjectId;
                }
                $complexObjectId++;
            }
        }
        
        return $flats;
    }
}
