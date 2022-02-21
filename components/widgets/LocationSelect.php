<?php

namespace app\components\widgets;

use yii\base\Widget;
use app\models\City;
use app\components\CityLocation;

/**
 * Widget for select location
 */
class LocationSelect extends Widget
{
    public $cities;

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this->render('/widgets/location-select', [
            'cities' => $this->cities,
            'selectedCity' => CityLocation::get()
        ]);
    }
}