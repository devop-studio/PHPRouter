<?php

namespace Millennium\Exceptions;

class AccessDeniedException extends \Exception
{
    
    public function __construct($message = "Access denied", $code = 401, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
