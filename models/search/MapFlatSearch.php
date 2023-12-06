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
            $result[$item->newbuildingComplex->id]['id'] = $item->newbuildingComplex->id;
            $result[$item->newbuildingComplex->id]['name'] = $item->newbuildingComplex->name;
            $result[$item->newbuildingComplex->id]['latitude'] = $item->newbuildingComplex->latitude;
            $result[$item->newbuildingComplex->id]['longitude'] = $item->newbuildingComplex->longitude;
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
