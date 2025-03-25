<?php

namespace SuperVMar\Shared\Domain\Exception;

use Exception;
use Throwable;

class InvalidValueException extends Exception
{
    public function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}