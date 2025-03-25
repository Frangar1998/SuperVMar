<?php

namespace SuperVMar\Shared\Domain\Exception;

use RuntimeException;
use SuperVMar\Shared\Domain\Bus\Command\Command;

class CommandNotRegisteredException extends RuntimeException
{
    private const MESSAGE = 'The command <%s> has no command handler';

    public function __construct(Command $command)
    {
        $commandClass = get_class($command);
        $message = sprintf(self::MESSAGE, $commandClass);
        parent::__construct($message);
    }
}