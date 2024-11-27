<?php

namespace app\models\search;

/**
 * Class for search flats on maps
 */
class MapFlatSearch extends AdvancedFlatSearch
{    
    public $selected_points = [];
    
    /**
     * {@inheritdoc}
     */
    protected function getResultWhenBadData()
    {
        return NULL;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function getResult($query, $pageParam)
    {
        $result = [];
        $queryResult = $query
            ->andWhere('nc2.latitude IS NOT NULL')
            ->andWhere('nc2.longitude IS NOT NULL')
            ->all();
        
        foreach ($queryResult as $item) {
            if(!array_key_exists($item->newbuildingComplex->id, $result)) {
                $result[$item->newbuildingComplex->id] = [];
            }

            /** Calculate some params for Newbuildingcomplex */
            // Price Range
            if(!array_key_exists('priceRange', $result[$item->newbuildingComplex->id])) {
                $prices = ['min' => 0, 'max' => 0];
                $minPrice = $item->newbuildingComplex->minFlatPrice;
                if ($minPrice > 0) $prices['min'] = round($minPrice);
                $maxPrice = $item->newbuildingComplex->maxFlatPrice;
                if ($maxPrice > 0) $prices['max'] = round($maxPrice);
                $priceRange = \Yii::$app->formatter->asCurrencyRange($prices['min'], $prices['max']);
            }

            // Flats by Room
            if(!array_key_exists('flats_by_room', $result[$item->newbuildingComplex->id])) {
                $pricesByRoom = [];
                // add values for apartments with a sertain amount of rooms
                $roomsAmount = [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5];
                foreach ($roomsAmount as $roomind => $roomval) {
                    $roomItem = $roomind.'Room';
                    $$roomItem = false;
                    if (!is_null($item->newbuildingComplex->getMinFlatPriceForRooms($roomval)) && !is_null($item->newbuildingComplex->getMaxFlatPriceForRooms($roomval))) {
                        $$roomItem = [
                            'search_url' => '/site/search?AdvancedFlatSearch[roomsCount][0]='.$roomval.'&AdvancedFlatSearch[flatType]='.AdvancedFlatSearch::FLAT_TYPE_STANDARD.'&AdvancedFlatSearch[newbuilding_complex][]='.$item->newbuildingComplex->id.'&AdvancedFlatSearch[developer][]='.$item->newbuildingComplex->developer->id,
                            'label' => $roomval.' - комнатные',
                            'price' => \Yii::$app->formatter->asCurrencyRange(round($item->newbuildingComplex->getMinFlatPriceForRooms($roomval)), round($item->newbuildingComplex->getMaxFlatPriceForRooms($roomval)), 'руб.')
                        ];
                    }
                    array_push($pricesByRoom, $$roomItem);
                }
            }

            $result[$item->newbuildingComplex->id]['id'] = $item->newbuildingComplex->id;
            $result[$item->newbuildingComplex->id]['name'] = $item->newbuildingComplex->name;
            if(!array_key_exists('address', $result[$item->newbuildingComplex->id])) {
                $result[$item->newbuildingComplex->id]['address'] = $item->newbuildingComplex->address;
            }
            $result[$item->newbuildingComplex->id]['latitude'] = $item->newbuildingComplex->latitude;
            $result[$item->newbuildingComplex->id]['longitude'] = $item->newbuildingComplex->longitude;
            if(!array_key_exists('logo', $result[$item->newbuildingComplex->id])) {
                $result[$item->newbuildingComplex->id]['logo'] = $item->newbuildingComplex->logo;
            }
            if(!array_key_exists('images', $result[$item->newbuildingComplex->id])) {
                $result[$item->newbuildingComplex->id]['images'] = $item->newbuildingComplex->images;
            }
            if(!array_key_exists('areaRange', $result[$item->newbuildingComplex->id])) {
                $result[$item->newbuildingComplex->id]['areaRange'] = \Yii::$app->formatter->asAreaRange($item->newbuildingComplex->minFlatArea, $item->newbuildingComplex->maxFlatArea);
            }
            if(!array_key_exists('priceRange', $result[$item->newbuildingComplex->id])) {
                $result[$item->newbuildingComplex->id]['priceRange'] = $priceRange;
            }
            if(!array_key_exists('freeFlats', $result[$item->newbuildingComplex->id])) {
                $result[$item->newbuildingComplex->id]['freeFlats'] = \Yii::$app->formatter->asPercent($item->newbuildingComplex->freeFlats);
            }
            if(!array_key_exists('flats_by_room', $result[$item->newbuildingComplex->id])) {
                $result[$item->newbuildingComplex->id]['flats_by_room'] = $pricesByRoom;
            }
            if(!array_key_exists('nearestDeadline', $result[$item->newbuildingComplex->id])) {
                $result[$item->newbuildingComplex->id]['nearestDeadline'] = !is_null($item->newbuildingComplex->nearestDeadline) ? \Yii::$app->formatter->asQuarterAndYearDate($item->newbuildingComplex->nearestDeadline) : 'нет данных';
            }
            $result[$item->newbuildingComplex->id]['flats'][] = $item;
        }

        return count($result) > 0 ? array_values($result) : [];
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFilters($query)
    {
        parent::applyFilters($query);

        if (count($this->selected_points) > 0) {
            $polygon = 'Polygon((';
            foreach ($this->selected_points as $point) {
                $polygon .= "{$point}, ";
            }
            $polygon .= "{$this->selected_points[0]}))";            
            $query->andWhere("ST_Contains(ST_GeomFromText('$polygon'), ST_GeomFromText(concat('POINT(', nc2.longitude, ' ', nc2.latitude, ')')))");
        }
    }
    
    /**
     * {@inheritdoc}
     */
    protected function fillAttributes($params, $isReturnFalseWhenNoData)
    {
        if (!parent::fillAttributes($params, $isReturnFalseWhenNoData)) {
            return false;
        } else {
            $form = (new \ReflectionClass($this))->getShortName();
            
            if (isset($params[$form]['selected_points']) && count($params[$form]['selected_points']) > 1) {
                $this->selected_points = $params[$form]['selected_points'];
                $this->selected_points = array_values(array_filter($this->selected_points, function ($value) { return $value !== ''; }));
            }
        
            return true;
        }
    }
}
