<?php

namespace app\commands;

use app\models\Offer;
use yii\console\Controller;

/**
 * Manage favorite flats
 */
class OfferController extends Controller
{
    const RETURN_OK = 0;    
    const RETURN_ERROR = 1;
    
    private $lockFile;
        
    /**
     * Remove expired offers
     */
    public function actionRemoveExpired()
    {
        $this->lockFile = \Yii::getAlias("@runtime/expired-offer-remove.lock");
        
        if (file_exists($this->lockFile)) {
            echo "Удаление не актуальных КП не выполнено,так как ещё выполняется предыдущее удаление\n";
            return self::RETURN_ERROR;
        }
        
        pcntl_signal(SIGINT, function ($signo) {
            if (file_exists($this->lockFile)) {
                unlink($this->lockFile);
            }
        });
        
        touch($this->lockFile);
        
        $period = 60 * 60 * 24 * 30 * 3;
                
        Offer::deleteAll("TO_SECONDS(NOW()) - TO_SECONDS(visited_at) >= {$period}");
        
        unlink($this->lockFile);
        
        return self::RETURN_OK;
    }
}
