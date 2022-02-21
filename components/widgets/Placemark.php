<?php

namespace app\components\widgets;

use yii\base\Widget;

/**
 * Widget for view image in model window
 */
class Placemark extends Widget
{
    public $longitude;
    public $address;
    public $latitude;
    
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this->render('/widgets/placemark', [
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'address' => $this->address
        ]);
    }
}
