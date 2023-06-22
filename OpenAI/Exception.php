<?php

namespace Abramovku\OpenAI;

class Exception extends \Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        error_log('OpenAI:' . $message, 0);

        parent::__construct($message, $code, $previous);
    }
}