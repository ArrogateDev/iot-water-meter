<?php

namespace App\Exceptions\WebSocket;

use Throwable;

class TokenExpiredException extends \Exception
{
    public function __construct($msg, $code, Throwable $previous = null)
    {
        parent::__construct($msg, $code, $previous);
    }
}
