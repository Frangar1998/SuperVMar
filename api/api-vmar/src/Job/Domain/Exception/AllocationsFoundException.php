<?php

namespace SuperVMar\Job\Domain\Exception;

use Exception;

class AllocationsFoundException extends Exception
{
    private const string MESSAGE = 'Cannot delete a job with existing allocations.';

    public function __construct()
    {
        parent::__construct(self::MESSAGE, 0);
    }
}