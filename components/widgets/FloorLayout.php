<?php

namespace app\components\widgets;

use app\models\Flat;
use yii\base\Widget;

/**
 * Widget for view floor layout
 */
class FloorLayout extends Widget
{
    const FLAT_VIEW_MODE = 1;
    const FLAT_SELECT_MODE = 2;
    const FLOOR_VIEW_MODE = 3;
    
    const COLORS = [
        /*Flat::STATUS_SALE => '00ff00',
        Flat::STATUS_RESERVED => '808080',
        Flat::STATUS_SOLD => 'ff0000',*/
    ];
    
    public $flat;
    public $floorLayout = NULL;
    public $mode = NULL;
    
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if (is_null($this->mode)) {
            $this->mode = self::FLAT_VIEW_MODE;
        }
        
        switch ($this->mode) {
            case self::FLAT_VIEW_MODE:
                return $this->render('/widgets/floor-layout', [
                    'id' => $this->flat->id,
                    'image' => !is_null($this->flat->floor_layout) || !is_null($this->flat->floor_layout) 
                        ? $this->flat->floor_layout 
                        : (!is_null($this->flat->floorLayout) ? $this->flat->floorLayout->image : ''),
                    'mode' => $this->mode,
                    'coords' => (is_null($this->flat->floor_position) || is_null($this->flat->floorLayout) || is_null($this->flat->floorLayout->map) || !isset($this->flat->floorLayout->mapArray[$this->flat->floor_position])) ? '' : $this->flat->floorLayout->mapArray[$this->flat->floor_position],
                    'color' => self::COLORS[$this->flat->status],
                    'status' => Flat::$status[$this->flat->status],
                ]);
            case self::FLAT_SELECT_MODE:
                return $this->render('/widgets/floor-layout', [
                    'id' => $this->flat->id,
                    'image' => !is_null($this->flat->floorLayout) ? $this->flat->floorLayout->image : '',
                    'mode' => $this->mode,
                    'map' => !is_null($this->flat->floorLayout) ? $this->flat->floorLayout->mapArray : NULL,
                    'flat' => $this->flat,
                    'busyPositions' => $this->flat->newbuilding->getFloorPositionsForSectionAndFloor($this->flat->section, $this->flat->floor, $this->flat->id),
                ]);
            case self::FLOOR_VIEW_MODE:
                return $this->render('/widgets/floor-layout', [
                    'id' => $this->floorLayout->id,
                    'image' => $this->floorLayout->image,
                    'mode' => $this->mode,
                    'map' => !is_null($this->floorLayout) ? $this->floorLayout->mapArray : NULL,
                ]);
        }
    }
}
