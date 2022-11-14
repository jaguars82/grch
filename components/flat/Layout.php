<?php

namespace app\components\flat;

class layout
{
    /**
     * create new svg image of floor layout with selected flat
     */
    public function createFloorLayoutWithSelectedFlat ($flat)
    {
        $originalSVGPath = \Yii::getAlias("@webroot/uploads/".$flat->floorLayout->image);
        $newSVGFilePath = \Yii::getAlias("@webroot/uploads/floorlayout-selections/".$flat->floorLayout->image);

        if (!file_exists($newSVGFilePath) && !is_null($flat->floorLayout) && file_exists($originalSVGPath)) {

            $fileContent = file_get_contents($originalSVGPath);
            $fileContent = str_replace('</svg>', '<polygon style="fill: rgba(0, 128, 1, 0.3);" points="'.$flat->layout_coords.'"></polygon></svg>', $fileContent);
            
            file_put_contents($newSVGFilePath, $fileContent);
        }
    }
}