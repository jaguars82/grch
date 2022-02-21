<?php

namespace app\models\query;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\app\models\Contact]].
 */
class ContactQuery extends ActiveQuery
{   
    /**
     * Get contacts for given newbuilding complex
     * 
     * @param mixed $newbuildingComplex newbuilding complex ID
     * @return yii\db\ActiveQuery
     */
    public function forNewbuildingComplex($newbuildingComplex)
    {
        if (is_null($newbuildingComplex)) {
            return $this;
        }
        
        return $this->join('INNER JOIN', 'newbuilding_complex_contact as a1', 'contact.id = a1.contact_id')
            ->andWhere('a1.newbuilding_complex_id=:newbuildingComplex', [':newbuildingComplex' => (int)$newbuildingComplex]);
    }
    
    /**
     * Get contacts for given developer
     * 
     * @param mixed $developer developer ID
     * @return yii\db\ActiveQuery
     */
    public function forDeveloper($developer)
    {
        if (is_null($developer)) {
            return $this;
        }
        
        return $this->join('INNER JOIN', 'developer_contact as a2', 'contact.id = a2.contact_id')
            ->andWhere('a2.developer_id=:developer', [':developer' => (int)$developer]);
    }
}
