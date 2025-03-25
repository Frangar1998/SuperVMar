<?php

namespace SuperVMar\Shared\Domain\Exception;

use Exception;

class MandatoryParamsException extends Exception
{
    private const MESSAGE = 'The following params are mandatory: %s';

    public function __construct(string $params)
    {
        $message = sprintf(self::MESSAGE, $params);
        parent::__construct($message);
    }
}