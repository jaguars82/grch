<?php

namespace app\models\query;

use app\models\Flat;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\app\models\Flat]].
 */
class FlatQuery extends ActiveQuery
{
    /**
     * Get analogs flats
     *
     * @param app\models\Flat $model flat
     * @param int $costInterval cost interval
     * @return yii\db\ActiveQuery
     */
    public function analogs($model, $costInterval = 200000)
    {
        // $minPrice = $model->cashPriceWithDiscount - $costInterval;
        // $maxPrice = $model->cashPriceWithDiscount + $costInterval;
        $minPrice = $model->price_cash - $costInterval;
        $maxPrice = $model->price_cash + $costInterval;

        return $this->andWhere("id != {$model->id}")
            ->andWhere("rooms != 0")
            ->andWhere("rooms >= {$model->rooms}")
            ->andWhere("price_cash > $minPrice AND price_cash < $maxPrice");
    }

    /**
     * Get flats for given newbuilding complex
     *
     * @param integer $newbuildingComplexId
     * @return yii\db\ActiveQuery
     */
    public function forNewbuildingComplex($newbuildingComplexId)
    {
        return $this->join('INNER JOIN', 'newbuilding', 'newbuilding.id = flat.newbuilding_id')
                ->andWhere(['newbuilding.newbuilding_complex_id' => $newbuildingComplexId]);
    }

    /**
     * Get flats for given section
     *
     * @param int $section section number
     * @return yii\db\ActiveQuery
     */
    public function forSection($section)
    {
        return $this->andWhere(['section' => $section])->orderBy(['floor' => SORT_DESC, 'number' => SORT_DESC]);
    }

    /**
     * Get only active flats
     *
     * @return yii\db\ActiveQuery
     */
    public function onlyActive()
    {
        return $this->andWhere(['flat.status' => Flat::STATUS_SALE])
					->andWhere("price_cash > 0");
    }

    /**
     * Get only reserved flats
     *
     * @return yii\db\ActiveQuery
     */
    public function onlyReserved()
    {
        return $this->andWhere(['flat.status' => Flat::STATUS_RESERVED]);
    }

    /**
     * Get flats with not null price
     *
     * @return yii\db\ActiveQuery
     */
    public function withNonNullPrice()
    {
        return $this->andWhere("price_cash > 0");
    }

    /**
     * Get flats with not null area
     *
     * @return yii\db\ActiveQuery
     */
    public function withNonNullArea()
    {
        return $this->andWhere("area > 0");
    }

    /**
     * Get flats with not null floor
     *
     * @return yii\db\ActiveQuery
     */
    public function withNonNullFloor()
    {
        return $this->andWhere("floor > 0");
    }

    /**
     * Get flats with only active newbuildigns
     *
     * @return yii\db\ActiveQuery
     */
    public function withOnlyActiveNewbuildings()
    {
        return $this->join('INNER JOIN', 'newbuilding', 'newbuilding.id = flat.newbuilding_id')
            ->join('INNER JOIN', 'newbuilding_complex', 'newbuilding_complex.id = newbuilding.newbuilding_complex_id')
            ->andWhere(['newbuilding_complex.active' => true]);
    }
}
