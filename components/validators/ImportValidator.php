<?php

namespace app\components\validators;

use app\components\import\ImportServiceInterface;
use app\models\Import;
use yii\validators\Validator;

/**
 * Implements validation for flat validation
 */
class ImportValidator extends Validator
{    
    const NOEXISTS_ERROR = -1;    
    const NOIMPORT_ERROR = -2;
    const TYPE_ERROR = -3;
    
    public static $errorMessages = [
        self::NOEXISTS_ERROR => 'Класс, реализующий импорт, не существует',
        self::NOIMPORT_ERROR => 'Класс не реализует импорт',
        self::TYPE_ERROR => 'Класс, реализующий импорт, не поддерживает данный тип импорта',
    ];
    
    /**
     * Execute validation
     * 
     * @param string $class
     * @param string $type
     * @return mix
     */
    public static function execute($class, $type)
    {
        if (!Import::isAlgorithmExists($class)) {
            return self::NOEXISTS_ERROR;
        }

        if (!Import::isAlgorithmValid($class)) {
            return self::NOIMPORT_ERROR;
        }

        if (($type == Import::IMPORT_TYPE_MANUAL && !Import::isSupport($class, ImportServiceInterface::SUPPORT_MANUAL))
            || ($type == Import::IMPORT_TYPE_AUTO && !Import::isSupport($class, ImportServiceInterface::SUPPORT_AUTO)))
        {
            return self::TYPE_ERROR;
        }
        
        return true;
    }
    
    public function validateAttribute($model, $attribute)
    {        
        if (($result = self::execute($model->$attribute, $model->type)) < 0) {
            $this->addError($model, $attribute, self::$errorMessages[$result]);
        }
    }
    
    public function clientValidateAttribute($model, $attribute, $view)
    {
        return <<<JS
typeText = $('#import-type option:selected').val();
        
if (typeText == 0) {
    return;
}
deferred.push($.post('/import/check-import', {class: value, type: typeText})
    .fail(function(data) {
        messages.push(data.responseJSON.message);
    }))
JS;
    }
}
