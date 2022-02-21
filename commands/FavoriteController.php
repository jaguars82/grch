<?php

namespace app\commands;

use app\models\Favorite;
use yii\console\Controller;

/**
 * Manage favorite flats
 */
class FavoriteController extends Controller
{
    const RETURN_OK = 0;    
    const RETURN_ERROR = 1;
    
    private $lockFile;
        
    /**
     * Archive sold favorite flats
     */
    public function actionArchive()
    {
        $this->lockFile = \Yii::getAlias("@runtime/favorite-archive.lock");
        
        if (file_exists($this->lockFile)) {
            echo "Архивация избранных квартир не выполнена,так как ещё выполняется предыдущая архивация\n";
            return self::RETURN_ERROR;
        }
        
        pcntl_signal(SIGINT, function ($signo) {
            if (file_exists($this->lockFile)) {
                unlink($this->lockFile);
            }
        });
        
        touch($this->lockFile);
        
        $favorites = Favorite::find()->onlyActive()->forSoldFlats()->all();
        
        foreach ($favorites as $favorite) {
            $favorite->archive()->save();
        }
        
        unlink($this->lockFile);
        
        return self::RETURN_OK;
    }
}
