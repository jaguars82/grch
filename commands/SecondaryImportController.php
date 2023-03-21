<?php
declare(ticks = 1);

namespace app\commands;

use app\components\exceptions\AppException;
use app\models\service\Agency;
use yii\console\Controller;
use yii\web\NotFoundHttpException;

/**
 * Import advertisements data
 */
class SecondaryImportController extends Controller
{
    const RETURN_OK = 0;    
    const RETURN_ERROR_NOT_FOUND = 1;
    const RETURN_ERROR_NO_IMPORT = 2;
    const RETURN_ERROR_STILL_RUN = 3;
    
    private $lockFile;
    
    public function actionRun($id)
    {
        date_default_timezone_set('Europe/Moscow');
        
        if (($agency = Agency::findOne($id)) === null) {
            echo "Агентство с ID $id не найдено\n";
            return self::RETURN_ERROR_NOT_FOUND;
        }
        
        if (is_null($import = $agency->import)) {
            echo "Для агентства не настроен импорт\n";
            return self::RETURN_ERROR_NO_IMPORT;
        }
        
        $algorithm = $import->algorithmAsObject;
        
        $this->lockFile = \Yii::getAlias("@runtime/secondary-import-$id.lock");
        
        if (file_exists($this->lockFile)) {
            echo "Новый импорт не выполнен,так как предыдущий импорт для агентства ещё выполняется или завершился с ошибкой\n";
            return self::RETURN_ERROR_STILL_RUN;
        }
        
        pcntl_signal(SIGINT, function ($signo) {
            if (file_exists($this->lockFile)) {
                unlink($this->lockFile);
            }
        });
        
        touch($this->lockFile);

        $endpoints = explode(', ', $import->endpoint);

        foreach ($endpoints as $endpoint) {
            $endpointParams = explode('|algo:', $endpoint);
            if(count($endpointParams) > 1 && !empty($endpointParams[1])) {
                $algorithm = $import->getAlgorithmAsObject($endpointParams[1]);
                $data = $algorithm->getAndParse($endpointParams[0]);
                $agency->import($data);
            } else {
                $data = $algorithm->getAndParse($endpoint);
                $agency->import($data);
            }
        }
        
        unlink($this->lockFile);
        
        return self::RETURN_OK;
    }
}