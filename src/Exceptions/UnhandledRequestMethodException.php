<?php

namespace Genesis\Api\Mocker\Exceptions;

use Exception;

/**
 * UnhandledRequestMethodException class.
 */
class UnhandledRequestMethodException extends Exception
{
    public function __constuct($method)
    {
        parent::__construct("Request method '$method' not implemented.");
    }
}
