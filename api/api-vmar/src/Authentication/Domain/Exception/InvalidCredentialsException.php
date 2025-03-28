<?php

namespace SuperVMar\Authentication\Domain\Exception;

use Exception;

final class InvalidCredentialsException extends Exception
{
    private const string MESSAGE = 'Invalid credentials.';

    public function __construct()
    {
        parent::__construct(self::MESSAGE);
    }
}