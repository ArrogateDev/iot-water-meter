<?php

namespace App\Exceptions\WebSocket;

use Throwable;

class WebSocketException extends \Exception
{
    protected $exception;

    protected $type;

    public function __construct($msg, $code, $type, Throwable $previous = null)
    {
        parent::__construct($msg, $code);
        $this->exception = $previous;
        $this->type = $type;
    }

    public function getException()
    {
        return $this->exception;
    }

    public function getType()
    {
        return $this->type;
    }
}
