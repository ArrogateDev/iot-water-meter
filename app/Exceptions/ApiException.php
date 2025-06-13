<?php

namespace App\Exceptions;

use Throwable;

class ApiException extends \Exception
{
    public function __construct($msg, $code, Throwable $previous = null)
    {
        parent::__construct($msg, $code, $previous);
    }
}
