<?php

namespace Millennium\Exceptions;

class RouteNotFoundException extends \Exception
{

    public function __construct($message = "Route not found", $code = 404, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
