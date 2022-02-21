<?php

namespace app\components\exceptions;

/**
 * Description of AppException
 */
class AppException extends \Exception
{
    /**
     * {@inheritdoc}
     */
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
