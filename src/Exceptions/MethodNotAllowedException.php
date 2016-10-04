<?php

namespace Millennium\Router\Exceptions;

class MethodNotAllowedException extends \Exception
{
    public function __construct($message = 'Access denied', $code = 405, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
