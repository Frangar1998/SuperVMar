<?php

namespace SuperVMar\Shared\Domain\Exception;

use Exception;
use Throwable;

class InternalErrorException extends Exception
{
    private const string MESSAGE = 'Internal error occurred.';

    public function __construct(?string $message = null, ?Throwable $previous = null)
    {
        parent::__construct($message ?? self::MESSAGE, 0, $previous);
    }
}