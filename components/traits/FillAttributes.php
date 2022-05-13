<?php

namespace app\components\traits;

/**
 * Trait adding function for fill attributes
 */
trait FillAttributes 
{
    /**
     * Fill model's attributes from array
     * 
     * @param array $data
     * @param array $exceptFields
     * @return mixed
     */
    public function fill($data = [], $exceptFields = [])
    {       
        $data = array_diff_key($data, array_flip($exceptFields));
        
        $this->attributes = array_uintersect_assoc($data, $this->attributes, function ($a, $b) { 
            return $a == $b;  
        });
        
        return $this;
    }
}
