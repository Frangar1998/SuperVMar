<?php

namespace SuperVMar\Shared\Domain\Exception;

use Exception;

class InvalidUuidValueException extends Exception
{
    private const MESSAGE = 'The value %s is not a valid <Uuid>.';

    public function __construct(string $value)
    {
        $message = sprintf(self::MESSAGE, $value);
        parent::__construct($message);
    }
}