<?php

namespace app\components;

/**
 * Format and serve things about locations
 */
class LocationHelper
{
    const REGION_CODE_VORONEZH = 36;

    public static $regions_map = [
        self::REGION_CODE_VORONEZH => ['Воронеж', 'воронеж', 'Воронежская', 'воронежская', 'Воронежская область', 'воронежская область'],
    ];

    /**
     * Map of districts (both for city districts and region districts) of the region
     * Important: the last key if districts array - is the defualt district key of a region (we use it if the particular district hasn't been detected)
     */
    public static $city_districts_map = [
        self::REGION_CODE_VORONEZH => [
            'КМ' => ['Коминтерновский', 'коминтерновский'],
            'ЛН' => ['Ленинский', 'ленинский'],
            'ЦН' => ['Центральный', 'центральный'],
            'ЖД' => ['Железнодорожный', 'железнодорожный'],
            'ЛВ' => ['Левобережный', 'левобережный'],
            'СВ' => ['Советский', 'советский'],
            'СМ' => ['Семилукский', 'cемилукский'],
            'РМ' => ['Рамонский', 'рамонский'],
            'НВ' => ['Новоусманский', 'новоусманский'],
            'ХХ' => ['Хохольский', 'хохольский'],
            'НД' => ['Нижнедевицкий', 'нижнедевицкий'],
            'ОБЛ' => [], // - default key
        ]
    ];

    /**
     * Finds a key in regions_map by region name
     */
    function findRegionKey($inputString) {
        $normalizedInput = mb_strtolower(trim($inputString));
        
        foreach (self::$regions_map as $key => $variants) {
            foreach ($variants as $variant) {
                if (mb_strpos($normalizedInput, mb_strtolower($variant)) !== false) {
                    return $key;
                }
            }
        }
        
        return null; // If no results
    }

    /**
     * Finds district (or city district) code by region code and district name
     * If no matches, returns the last (default) key of regions array
     */
    public static function findDistrictCode($regionKey, $inputString)
    {
                
        // Check if the key exists in city_districts_map
        if (!isset(self::$city_districts_map[$regionKey])) {
            return null;
        }
        
        // Get the map of region districts
        $districtsMap = self::$city_districts_map[$regionKey];

        if (is_null($inputString)) {
            return array_key_last($districtsMap);
        }

        $normalizedInput = mb_strtolower(trim($inputString));
        
        foreach ($districtsMap as $code => $names) {
            foreach ($names as $name) {
                if (mb_strpos($normalizedInput, mb_strtolower($name)) !== false) {
                    return $code;
                }
            }
        }
        
        // If no results - return the last key (we use it as default)
        return array_key_last($districtsMap);
    }

}