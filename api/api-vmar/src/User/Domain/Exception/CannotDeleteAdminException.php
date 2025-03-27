<?php

namespace SuperVMar\User\Domain\Exception;

use Exception;

final class CannotDeleteAdminException extends Exception
{
    private const string MESSAGE = 'User admin cannot be deleted.';

    public function __construct()
    {
        parent::__construct(self::MESSAGE);
    }
}