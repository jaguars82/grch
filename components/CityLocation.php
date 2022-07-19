<?php

namespace app\components;

use yii\base\Component;
use app\models\City;

/**
 * City location component
 */
class CityLocation extends Component 
{
    protected static $city = null;
    protected static $defaultCity = 1;

    /**
     * Set location to cookie from request
     * 
     * @return void
     */
    public static function setFromRequest()
    {
        $selectedCity = null;
        $cookie = \Yii::$app->request->cookies;
        $queryParams = \Yii::$app->request->queryParams;
        $defaultCityId = ($cookie->has('selected-city-' . \Yii::$app->user->id)) ? $cookie->get('selected-city-' . \Yii::$app->user->id) : static::$defaultCity;
        $defaultCityId = static::$defaultCity;
        $queryCityId = isset($queryParams['city']) && !empty($queryParams['city']) ? $queryParams['city'] : NULL;

        if(!is_null($queryCityId)) {
            $selectedCity = City::findOne($queryCityId);
            
            if(!is_null($selectedCity)) {
                \Yii::$app->response->cookies->add(new \yii\web\Cookie([
                    'name' => 'selected-city-' . \Yii::$app->user->id,
                    'value' => $queryCityId,
                ]));
            }
        }

        if(is_null($selectedCity)) {
            $selectedCity = City::findOne($defaultCityId);
        }

        static::$city = $selectedCity;
    }

    /**
     * Get current location
     * 
     * @return City
     */
    public static function get()
    {
        if(!is_null(static::$city)) {
            return static::$city;
        } else {
            static::setFromRequest();

            return static::$city;
        }
    }
}