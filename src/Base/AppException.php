<?php

namespace Genesis\Api\Mocker\Base;

/**
 * AppException class.
 */
class AppException extends \Exception
{
    public function __construct($msg)
    {
        error_log('[ERROR]: '.$msg);
        parent::__construct($msg);
    }
}
