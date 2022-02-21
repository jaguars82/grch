<?php
declare(ticks = 1);

namespace app\commands;

use app\components\exceptions\AppException;
use app\models\service\Developer;
use yii\console\Controller;
use yii\web\NotFoundHttpException;

/**
 * Import flat's data
 */
class ImportController extends Controller
{
    const RETURN_OK = 0;    
    const RETURN_ERROR_NOT_FOUND = 1;
    const RETURN_ERROR_NO_IMPORT = 2;
    const RETURN_ERROR_NO_SUPPORT = 3;
    const RETURN_ERROR_STILL_RUN = 4;
    
    private $lockFile;
    
    public function actionRun($id)
    {
        date_default_timezone_set('Europe/Moscow');
        
        if (($developer = Developer::findOne($id)) === null) {
            echo "Застройщик с ID $id не найден\n";
            return self::RETURN_ERROR_NOT_FOUND;
        }
        
        if (is_null($import = $developer->import)) {
            echo "Для застройщика не настроен импорт\n";
            return self::RETURN_ERROR_NO_IMPORT;
        }
        
        $algorithm = $import->algorithmAsObject;
        
        if (!$algorithm->isSupportAuto()) {
            echo "Застройщик не поддерживает автоматический импорт\n";
            return self::RETURN_ERROR_NO_SUPPORT;
        }

        $this->lockFile = \Yii::getAlias("@runtime/import-$id.lock");
        
        if (file_exists($this->lockFile)) {
            echo "Новый импорт не выполнен,так как для застройщика ещё выполняется предыдущий импорт\n";
            return self::RETURN_ERROR_STILL_RUN;
        }
        
        pcntl_signal(SIGINT, function ($signo) {
            if (file_exists($this->lockFile)) {
                unlink($this->lockFile);
            }
        });
        
        touch($this->lockFile);
        
        $data = $algorithm->getAndParse($import->endpoint);
        $developer->import($data);
        
        unlink($this->lockFile);
        
        return self::RETURN_OK;
    }
}
