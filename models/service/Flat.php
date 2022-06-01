<?php

namespace app\models\service;

use Yii;
use app\models\FlatImage;
use app\models\Entrance;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for processing flats.
 */
class Flat extends \app\models\Flat
{
    /**
     * Create flat's entities
     * 
     * @param array $data
     * @return \app\models\service\Flat
     * @throws \Exception
     */
    public static function create($data)
    {
        $transaction = Yii::$app->db->beginTransaction();
            
        try {                
            $flat = (new Flat())->fill($data);

            // ID of an entrance
            // $flat->entrance_id = $data['entrance_id'];

            if($data['layout_type'] == 'euro') {
                $flat->is_euro = true;
                $flat->is_studio = false;
            } else if($data['layout_type'] == 'studio') {
                $flat->is_studio = true;
                $flat->is_euro = false;
            } else {
                $flat->is_studio = false;
                $flat->is_euro = false;
            }

            $flat->save();

            $actions = News::findAll($data['actions']);
            foreach ($actions as $action) {
                $flat->link('actions', $action);
            }
            
            foreach ($data['images'] as $image) {
                $flatImage = new FlatImage([
                    'image' => $image,
                ]);
                $flat->link('flatImages', $flatImage);
            }

            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
            
        return $flat;
    }
    
    /**
     * Edit flat's entities
     * 
     * @param array $data
     * @throws \Exception
     */
    public function edit($data)
    {
        if (isset($data['newbuilding_id'])) {
            unset($data['newbuilding_id']);
        }
            
        $transaction = Yii::$app->db->beginTransaction();
            
        try {
            $status = $this->status;
            $this->fill($data, ['layout', 'floor_layout', 'floor_position']);

            // ID of an entrance
            /* if (!empty($data['entrance_id'])) {
                $this->entrance_id = $data['entrance_id'];
            } */

            if ($data['is_layout_reset']) {
                $this->layout = NULL;
            } else {
                $this->layout = (!is_null($data['layout'])) ? $data['layout'] : $this->layout;
            }
            
            if($data['layout_type'] == 'euro') {
                $this->is_euro = true;
                $this->is_studio = false;
            } else if($data['layout_type'] == 'studio') {
                $this->is_studio = true;
                $this->is_euro = false;
            } else {
                $this->is_studio = false;
                $this->is_euro = false;
            }

            $flat = self::applyFlatPostionOnFloorLayout($this, $data, $status);
            $flat->save();

            $actions = ArrayHelper::getColumn($this->getActions()->asArray()->all(), 'id');
            $receivedActions = is_array($data['actions']) ? $data['actions'] : [];

            $addedActions = News::findAll(array_diff($receivedActions, $actions));
            foreach ($addedActions as $action) {
                $this->link('actions', $action);
            }

            $removedActions = News::findAll(array_diff($actions, $receivedActions));
            foreach ($removedActions as $action) {
                $this->unlink('actions', $action, true);
            }
            
            foreach ($this->flatImages as $flatImage) {
                if (!in_array(strval($flatImage->id), $data['savedImages'])) {
                    $flatImage->delete();
                }
            }
            
            foreach ($data['images'] as $image) {
                $flatImage = new FlatImage([
                    'image' => $image,
                ]);
                $this->link('flatImages', $flatImage);
            }

            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
    /**
     * Apply flat postion on given floor layout file
     * 
     * @param type $flat
     * @param type $data
     * @param type $flatStatus
     * @return type
     */
    public static function applyFlatPostionOnFloorLayout($flat, $data, $flatStatus)
    {
        if (((isset($data['floor_position']) && $flat->floor_position != $data['floor_position']) || $flatStatus != $data['status'])
            && !is_null($flat->floorLayout) 
            && !is_null($flat->floorLayout->image)
            && !is_null($flat->floorLayout)
            && !empty($flat->floorLayout->map)
        ) {
            if (isset($data['floor_position'])) {
                $flat->floor_position = $data['floor_position'];
            }
               
            $status = $flat->status;
            $filename = \Yii::getAlias('@webroot') . "/uploads/{$flat->floorLayout->image}";
            $map = $flat->floorLayout->mapArray;
            $flatPosition = is_null($flat->floor_position) || $flat->floor_position === '' ? NULL : $flat->floor_position;
            $floorLayoutFilename = "floor_layout_{$flat->id}_" . mt_rand() . ".png";
            $floorLayout = \Yii::getAlias("@webroot/uploads/{$floorLayoutFilename}");

            list($width, $height) = @getimagesize($filename);

            switch (exif_imagetype($filename)) {
                case IMAGETYPE_JPEG:
                    $src = imagecreatefromjpeg($filename);
                    break;
                case IMAGETYPE_PNG:
                    $src = imagecreatefrompng($filename);
                    break;
                case IMAGETYPE_GIF:
                    $src = imagecreatefromgif($filename);
                    break;
            }

            $dst = imagecreatetruecolor ($width , $height);
            imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $width, $height, $width, $height);        

            if (!is_null($map) && !is_null($flatPosition)) {
                switch ($status) {
                    case self::STATUS_SALE:
                        $color = imagecolorallocatealpha($dst, 0, 255, 0, 42);
                        break;
                    case self::STATUS_RESERVED:
                        $color = imagecolorallocatealpha($dst, 80, 80, 80, 42);
                        break;
                    case self::STATUS_SOLD:
                        $color = imagecolorallocatealpha($dst, 255, 0, 0, 42);
                        break;
                }

                $points = explode(',', $map[$flatPosition]);                      
                imagefilledpolygon($dst, $points, count($points)/2, $color);
            }

            imagesavealpha($dst, true);
            imagePng($dst, $floorLayout);
            imagedestroy($src);
            imagedestroy($dst);

            $floorLayout = $floorLayoutFilename;

            if (!is_null($flat->floor_layout)) {
                unlink(\Yii::getAlias("@webroot/uploads/{$flat->floor_layout}"));
            }

            $flat->floor_layout = $floorLayout;
        }
        
        return $flat;
    }
}
