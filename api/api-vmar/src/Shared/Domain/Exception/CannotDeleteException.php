<?php

namespace SuperVMar\Shared\Domain\Exception;

use Exception;

class CannotDeleteException extends Exception
{
    private const string MESSAGE = 'Cannot delete object with existing related objects.';

    public function __construct(?string $message = null)
    {
        parent::__construct($message ?? self::MESSAGE, 0);
    }
}