<?php

namespace app\components\flat;

use app\components\archive\interfaces\ArchiveInterface;
use app\components\exceptions\AppException;
use app\models\NewbuildingComplex;
use app\models\Newbuilding;
use app\models\FloorLayout;
use app\models\Flat;
use yii\base\Component;

/**
 * Import extracted archive to flat table
 */
class ImportArchive extends Component 
{
    public $extractPath;
    public $archive;

    private $newbuildingComplex;
    private $newbuilding;
    private $treeListFiles;
    private $errors = [];

    const MASK_FILENAME = 'mask.svg';
    const FLOOR_LAYOUT_FILENAME = 'floor.svg';

    public function __construct(ArchiveInterface $archive, NewbuildingComplex $newbuildingComplex) 
    {
        if(!$archive->isExtracted) {
            throw new \Exception('Archive not extracted');
        }

        $this->extractPath = $archive->extractPath;
        $this->archive = $archive;
        $this->newbuildingComplex = $newbuildingComplex;
        $this->treeListFiles = $archive->getTreeListFiles();

        parent::__construct();
    }

    /**
     * Check extracted archive
     * @return boolean $success
     */
    public function check() 
    {
        $newbuildingComplexName = array_key_first($this->treeListFiles);

        $positions = array_shift($this->treeListFiles);

        if(empty($positions)) {
            $this->errors[] = 'Список позиций пуст';
            return false;
        }

        foreach($positions as $position => $sections) {
            $newbuilding = Newbuilding::find()
                ->where([
                    'name' => $position,
                    'newbuilding_complex_id' => $this->newbuildingComplex->id
                ])
                ->one();
            
            if($newbuilding === null) {
                $this->errors[] = "Позиции '$position' нет в базе данных";
                return false;
            }

            if(count($sections) == 0) {
                $this->errors[] = "У позиции '$position' нет секций";
                return false;
            }

            foreach($sections as $sectionStr => $floors) {
                $section = intval(preg_replace('/[^0-9]/', '', $sectionStr));

                if(count($floors) == 0) {
                    $this->errors[] = "У позиции $position в $section подъезде нет этажей";
                    return false;
                }

                foreach($floors as $floor => $layouts) {
                    $floorRange = array_slice(explode('-', $floor), 2);

                    $positionCount = 0;
                    foreach($layouts as $layout) {
                        if(in_array($layout, [self::FLOOR_LAYOUT_FILENAME, self::MASK_FILENAME])) {
                            continue;
                        }
                        $positionCount++;

                        if($this->isNameSvgFile($layout) === false) {
                            $this->errors[] = "Файлы планировки должны иметь расширение svg ($layout)";
                        }

                        $layoutInfo = $this->getLayoutDataFromName(substr($layout, 0, -4));

                        $flats = Flat::find()
                            ->where([
                                'newbuilding_id' => $newbuilding->id, 
                                'section' => $section, 
                                //'area' => floatval($area),
                                //'rooms' => $layoutInfo['roomsCount'],
                            ])
                            ->andWhere(['>=', 'floor', $floorRange[0]])
                            ->andWhere(['<=', 'floor', $floorRange[1]])
                            ->andWhere(['like', 'area', $layoutInfo['area']])
                            ->all();

                        if($flats == null) {
                            if($layoutInfo['is_euro']) {
                                $roomType = 'квартиры с европланировкой';
                            } else if($layoutInfo['is_studio']) {
                                $roomType = 'квартиры-студии';
                            } else {
                                $roomType = 'квартиры';
                            }

                            $this->errors[] = "У позиции '$position' нет $roomType в $section подъезде на этажах с {$floorRange[0]} по {$floorRange[1]} с {$layoutInfo['roomsCount']} " . ($layoutInfo['roomsCount'] > 1 ? 'комнатами' : 'комнатой') . " {$layoutInfo['area']} кв.м";
                        }
                    }
                                        
                    if(!in_array(self::FLOOR_LAYOUT_FILENAME, $layouts)) {
                        $this->errors[] = "У позиции $position в $section подъезде в папке этажей с {$floorRange[0]} по {$floorRange[1]} нет поэтажной планировки";
                    }

                    if(!in_array(self::MASK_FILENAME, $layouts)) {
                        $this->errors[] = "У позиции $position в $section подъезде в папке этажей с {$floorRange[0]} по {$floorRange[1]} нет маски";
                    } else {
                        $this->checkMask([$newbuildingComplexName, $position, $sectionStr, $floor], $positionCount);
                    }
                }
            }
        }
  
        return count($this->errors) > 0 ? false : true;
    }

    /**
     * Import extracted archive
     * @return array $result import result
     */
    public function upload()
    {
        $result = [];
        $newbuildingComplexName = array_key_first($this->treeListFiles);

        foreach(array_shift($this->treeListFiles) as $position => $sections) {
            $this->newbuilding = Newbuilding::find()
                ->where([
                    'name' => $position,
                    'newbuilding_complex_id' => $this->newbuildingComplex->id
                ])
                ->one();
            
            foreach($sections as $section => $floors) {
                $sectionNum = $this->parseInt($section);

                foreach($floors as $floor => $layouts) {
                    $floorRange = $this->getFloorRangeFromString($floor);

                    $floorPath = implode('/', [$this->extractPath, $newbuildingComplexName, $position, $section, $floor]);
                    $layoutsData = $this->getGroupLayoutDataFromNames($layouts);

                    $floorLayoutPath = $floorPath . '/' . self::FLOOR_LAYOUT_FILENAME;
                    $newFloorLayoutFileName = $this->getHashName([$section, $floor, $this->newbuilding->id], 'svg');
                    $newFloorLayoutFilePath = \Yii::getAlias('@webroot/uploads/') . $newFloorLayoutFileName;

                    $this->replaceFile($floorLayoutPath, $newFloorLayoutFilePath);
                    $this->addFloorLayoutsFromSectionsAndFloors($newFloorLayoutFileName, $sectionNum, $floorRange);

                    $layoutCoords = $this->getLayoutCoordsFromMask($floorPath . '/' . self::MASK_FILENAME);
                    
                    $floorCoordsOnPositions = [];
                    foreach($layoutsData as $layoutData) {
                        $roomsCount = $layoutData[0]['roomsCount'];
                        $area = $layoutData[0]['area'];

                        $flats = Flat::find()
                            ->where([
                                'newbuilding_id' => $this->newbuilding->id, 
                                'section' => $sectionNum, 
                                //'area' => floatval($area),
                                //'rooms' => $roomsCount
                            ])
                            ->andWhere(['>=', 'floor', $floorRange[0]])
                            ->andWhere(['<=', 'floor', $floorRange[1]])
                            ->andWhere(['like', 'area', $area])
                            ->orderBy(['number' => SORT_ASC])
                            ->all();

                        if($flats == null) {
                            throw new AppException('Квартиры не найдены');
                        }

                        $flatsGroupByFloor = $this->groupFlats($flats, 'floor');

                        foreach($layoutData as $key => $layout) {
                            $layoutPath = $floorPath . '/' . $layout['file_name'];
                            $newLayoutName = $this->getHashName($layout['file_name'], 'svg');
                            $newLayoutPath = \Yii::getAlias('@webroot/uploads/') . $newLayoutName;

                            $this->replaceFile($layoutPath,  $newLayoutPath);

                            $layoutData[$key]['new_file_name'] = $newLayoutName;
                        }
                        
                        foreach($flatsGroupByFloor as $flats) {
                            foreach($flats as $key => $flat) {

                                if(!isset($layoutData[$key])) {
                                    continue;
                                }

                                $flat->layout = $layoutData[$key]['new_file_name'];
                                $layoutPosition = $layoutData[$key]['position'];

                                if(isset($layoutCoords[$layoutPosition - 1])) {
                                    $floorCoordsOnPositions[$layoutPosition] = $layoutCoords[$layoutPosition - 1];
                                    $flat->layout_coords = $floorCoordsOnPositions[$layoutPosition];
                                }

                                if($layoutData[$key]['is_euro'] == true) {
                                    $flat->is_euro = true;
                                } else if($layoutData[$key]['is_studio'] == true) {
                                    $flat->is_studio = true;
                                }

                                if($roomsCount !== $flat->rooms) {
                                    $flat->rooms = $roomsCount;
                                }

                                if(!$flat->save()) {
                                    throw new AppException('Ошибка при сохранении квартиры');
                                }
                            }
                        }
                    }

                    $result[] = [
                        'position' => $position,
                        'section' => $section,
                        'floor_range' => $floorRange,
                        'floor_layout' => $newFloorLayoutFileName,
                        'layout_coords' => $floorCoordsOnPositions
                    ];
                }
            }
        }
        
        return $result;
    }

    /**
     * Retrun layout info from parsing layout names
     * @param string $layoutName
     * @return array $layoutInfo
    */

    private function getLayoutDataFromName($layoutName)
    {
        $layoutInfo = [];

        $explodeLayoutName = explode('-', $layoutName);
        $layoutInfo['area'] = floatval(str_replace(',', '.', $explodeLayoutName[1]));
        $positionAndRooms = explode('.', $explodeLayoutName[0]);
        $layoutInfo['position'] = intval($positionAndRooms[0]);
        $rooms = trim($positionAndRooms[1]);

        $roomType = strtolower(preg_replace('/[^a-zа-я]/i', '', $rooms));
        $layoutInfo['roomsCount'] = intval(preg_replace('/[^0-9]/', '', $rooms));

        $layoutInfo['is_studio'] = false;
        $layoutInfo['is_euro'] = false;

        if($roomType == 'e' || $roomType == 'е') {
            if($layoutInfo['roomsCount'] > 1) {
                $layoutInfo['is_euro'] = true;
            } else {
                $layoutInfo['is_studio'] = true;
            }
        }

        if($roomType == 'c' || $roomType == 'с') {
            if($layoutInfo['roomsCount'] == 1) {
                $layoutInfo['is_studio'] = true;
            } else {
                $layoutInfo['is_euro'] = true;
            }
        }

        return $layoutInfo;
    }

    /**
     * Group flats models by field
     * @param $flats flats models
     * @param $field field name for grouping
     * @return array $flatsGroup flats group by field
     */
    private function groupFlats($flats, $field) 
    {
        $flatsGroup = [];
        foreach($flats as $flat) {
            if(isset($flat->{$field})) {
                $flatsGroup[$flat->{$field}][] = $flat;
            }
        }

        return $flatsGroup;
    }

    /**
     * Return layouts data from names
     * @param array $layouts list of layouts
     * @return array $layoutsData layouts data grouping by areas and rooms
     */
    private function getGroupLayoutDataFromNames($layouts) 
    {
        $layoutsData = [];

        foreach($layouts as $layoutName) {
            if(in_array($layoutName, [self::FLOOR_LAYOUT_FILENAME, self::MASK_FILENAME])) {
                continue;
            }
            
            $data = $this->getLayoutDataFromName($layoutName);
            $data['file_name'] = $layoutName;

            $layoutsData["{$data['area']}-{$data['roomsCount']}"][] = $data;
        }
 
        return $layoutsData;
    }

    /**
     * Add floor layouts by sections and floors
     * @param string $floorLayoutImage floor layout image name
     * @param integer $section section number
     * @param array $floorRange range of floors to add
     * @return boolean $success
     * @throws AppException if can't save FloorLayout model
     */
    private function addFloorLayoutsFromSectionsAndFloors($floorLayoutImage, $section, $floorRange)
    {   
        for($i = $floorRange[0]; $i <= $floorRange[1]; $i++) {
            $floorLayout = FloorLayout::findOne(['newbuilding_id' => $this->newbuilding->id, 'section' => $section, 'floor' => $i]);
            if($floorLayout == NULL) {
                $floorLayout = new FloorLayout;

                $floorLayout->newbuilding_id = $this->newbuilding->id;
                $floorLayout->section = strval($section);
                $floorLayout->floor = strval($i);
            }

            $floorLayout->image = $floorLayoutImage;
            
            if(!$floorLayout->save()) {
                throw new AppException('Ошибка при загрузке поэтажной планировки');
            }
        }

        return true;
    }

    
    /**
     * Check the mask file by the number of positions
     * @param array $floorParams current floor range params
     * @return array $positionCount positions count
     */
    private function checkMask($floorParams, $positionCount)
    {
        $relativeMaskPath = implode('/', $floorParams);
        $absoluteMaskPath = "{$this->extractPath}/{$relativeMaskPath}/" . self::MASK_FILENAME;

        if(!file_exists($absoluteMaskPath)) {
            $this->errors[] = "Не удалось проверить маску ({$relativeMaskPath}/" . self::MASK_FILENAME . ")";
            return;
        }

        $mask = new \DOMDocument();
        @$mask->loadHTMLFile($absoluteMaskPath, LIBXML_NOERROR | LIBXML_HTML_NOIMPLIED | LIBXML_NOENT | LIBXML_HTML_NODEFDTD);
        $layoutPoligons = $mask->getElementsByTagName('polygon');

        if($layoutPoligons->length === 0) {
            $this->errors[] = "В файле маски нет полигонов ({$relativeMaskPath}/" . self::MASK_FILENAME . ")";
        } else if($layoutPoligons->length !== $positionCount) {
            $this->errors[] = "В файле маски количество полигонов не соответствует количеству планировок ({$relativeMaskPath}/" . self::MASK_FILENAME . ")";
        }
    }

    /**
     * Get layout coords from mask file
     * @param string $maskPath path to mask
     * @return array $coords coords from mask file
     * @throws AppException if can't find polygon's in mask file
     */
    private function getLayoutCoordsFromMask($maskPath) 
    {
        $coords = [];

        $mask = new \DOMDocument();
        @$mask->loadHTMLFile($maskPath, LIBXML_NOERROR | LIBXML_HTML_NOIMPLIED | LIBXML_NOENT | LIBXML_HTML_NODEFDTD);
        $layoutPoligons = $mask->getElementsByTagName('polygon');

        if($layoutPoligons->length === 0) {
            throw new AppException('В маске нет полигонов позиций');
        }

        foreach ($layoutPoligons as $item) { 
            foreach($item->attributes as $attribute) {
                if($attribute->localName == 'points') {
                    $coords[] = $attribute->nodeValue;
                }
            }
        }

        return array_reverse($coords);
    }

    /**
     * Get hashed name for params
     * @param mixed $paramsToHash parameters or parameter from which the hash will be obtained
     * @param string $extension file extension
     * @return string $hashedName hashed name
     */
    private function getHashName($paramsToHash, $extension)
    {
        return md5((is_array($paramsToHash) ? implode('', $paramsToHash) : $paramsToHash) . time()) . '.' . $extension;
    }

    /**
     * Get range of floors from string
     * @param string $string string to prepare
     * @return array $floorRange range of floors
     */
    private function getFloorRangeFromString($string) 
    {
        $floorRange = array_slice(explode('-', $string), 2);

        return array_map(function ($floor) {
            return intval($floor);
        }, $floorRange);
    }

    /**
     * Move file to new path
     * @param string $path path to file
     * @param string $newPath path to move
     * @return boolean $success
     * @throws AppException if can't move file
     */
    private function replaceFile($path, $newPath)
    {
        if(!rename($path, $newPath)) {
            throw new AppException('Ошибка при копировании файла');
        }

        return true;
    }

    /**
     * Checks if svg file is by name
     * @param string $name file name
     * @return boolean $isSvgFile
     */
    private function isNameSvgFile($name)
    {
        return strpos($name, '.svg') !== false;
    }
    
    /**
     * String to int
     * @param string $string string
     * @return integer $numberFromString
     */
    private function parseInt($string)
    {
        return intval(preg_replace('/[^0-9]/', '', $string));
    }

    /**
     * Get import errors
     * @return array $errors list errors
     */
    public function getErrors() 
    {
        return $this->errors;
    }
}