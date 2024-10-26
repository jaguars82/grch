<?php

namespace app\components;

use yii\base\Component;
use app\models\Region;

/**
 * Region location component
 */
class RegionLocation extends Component 
{
    protected static $region = null;
    protected static $defaultRegion = 1;

    /**
     * Set location to cookie from request
     * 
     * @return void
     */
    public static function setFromRequest()
    {
        $selectedRegion = null;
        $cookie = \Yii::$app->request->cookies;
        $queryParams = \Yii::$app->request->queryParams;
        $defaultRegionId = ($cookie->has('selected-region-' . \Yii::$app->user->id)) ? $cookie->get('selected-region-' . \Yii::$app->user->id) : static::$defaultRegion;
        $defaultRegionId = static::$defaultRegion;
        $queryRegionId = isset($queryParams['region']) && !empty($queryParams['region']) ? $queryParams['region'] : NULL;

        if(!is_null($queryRegionId)) {
            $selectedRegion = Region::findOne($queryRegionId);
            
            if(!is_null($selectedRegion)) {
                \Yii::$app->response->cookies->add(new \yii\web\Cookie([
                    'name' => 'selected-region-' . \Yii::$app->user->id,
                    'value' => $queryRegionId,
                ]));
            }
        }

        if(is_null($selectedRegion)) {
            $selectedRegion = Region::findOne($defaultRegionId);
        }

        static::$region = $selectedRegion;
    }

    /**
     * Get current location
     * 
     * @return Region
     */
    public static function get()
    {
        if(!is_null(static::$region)) {
            return static::$region;
        } else {
            static::setFromRequest();

            return static::$region;
        }
    }
}