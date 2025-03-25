<?php

namespace SuperVMar\Shared\Domain\Exception;

use Exception;

class DuplicateItemException extends Exception
{
    private const MESSAGE = '<%s> with ID <%s> already exists.';

    public function __construct(string $item, string $id)
    {
        $message = sprintf(self::MESSAGE, $item, $id);
        parent::__construct($message);
    }
}