<?php

namespace app\components\traits;

/**
 * Trait adding function for fill attributes
 */
trait FlatPrices 
{
    /**
     * Getting minimum flat price
     * 
     * @return float
     */
    public function getMinFlatPrice()
    {        
        $flatsData = $this->getFlats()->select([
            'min(price_cash * (1 - discount)) as minPriceCash',
            'min(price_credit * (1 - discount)) as minPriceCredit',
        ])->withNonNullPrice()->one();
                
        return is_null($flatsData->minPriceCredit) ? $flatsData->minPriceCash : min($flatsData->minPriceCredit, $flatsData->minPriceCash);
    }
    
    /**
     * Getting maximum flat price
     * 
     * @return float
     */
    public function getMaxFlatPrice()
    {        
        $flatsData = $this->getFlats()->select([
            'max(price_cash * (1 - discount)) as maxPriceCash',
            'max(price_credit * (1 - discount)) as maxPriceCredit',
        ])->withNonNullPrice()->one();
                
        return is_null($flatsData->maxPriceCredit) ? $flatsData->maxPriceCash : max($flatsData->maxPriceCredit, $flatsData->maxPriceCash);
    }
    
    /**
     * Getting minimum flat price for rooms
     * 
     * @param $rooms integer
     * @return float
     */
    public function getMinFlatPriceForRooms($rooms)
    {        
        $flatsData = $this->getFlats()->select([
            'min(price_cash * (1 - discount)) as minPriceCash',
            'min(price_credit * (1 - discount)) as minPriceCredit',
        ])->withNonNullPrice()->andWhere([
            'rooms' => $rooms,
            'is_studio' => false,
        ])->one();

        return is_null($flatsData->minPriceCredit) ? $flatsData->minPriceCash : min($flatsData->minPriceCredit, $flatsData->minPriceCash);
    }

    /**
     * Getting maximum flat price for rooms
     * 
     * @param $rooms integer
     * @return float
     */
    public function getMaxFlatPriceForRooms($rooms)
    {        
        $flatsData = $this->getFlats()->select([
            'max(price_cash * (1 - discount)) as maxPriceCash',
            'max(price_credit * (1 - discount)) as maxPriceCredit',
        ])->withNonNullPrice()->andWhere([
            'rooms' => $rooms,
            'is_studio' => false
        ])->one();

        return is_null($flatsData->maxPriceCredit) ? $flatsData->maxPriceCash : max($flatsData->maxPriceCredit, $flatsData->maxPriceCash);
    }

    /**
     * Getting minimum studio flat price
     * 
     * @return float
     */
    public function getMinStudioFlatPrice()
    {        
        $flatsData = $this->getFlats()->select([
            'min(price_cash * (1 - discount)) as minPriceCash',
            'min(price_credit * (1 - discount)) as minPriceCredit',
        ])->withNonNullPrice()->andWhere([
            'is_studio' => true
        ])->one();

        return is_null($flatsData->minPriceCredit) ? $flatsData->minPriceCash : min($flatsData->minPriceCredit, $flatsData->minPriceCash);
    }

    /**
     * Getting maximum studio flat price
     * 
     * @return float
     */
    public function getMaxStudioFlatPrice()
    {        
        $flatsData = $this->getFlats()->select([
            'max(price_cash * (1 - discount)) as maxPriceCash',
            'max(price_credit * (1 - discount)) as maxPriceCredit',
        ])->withNonNullPrice()->andWhere([
            'is_studio' => true
        ])->one();

        return is_null($flatsData->maxPriceCredit) ? $flatsData->maxPriceCash : max($flatsData->maxPriceCredit, $flatsData->maxPriceCash);
    }
}
