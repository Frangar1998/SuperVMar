<?php

namespace SuperVMar\Shared\Domain\Exception;

use RuntimeException;
use SuperVMar\Shared\Domain\Bus\Event\DomainEvent;

class DomainEventNotRegisteredException extends RuntimeException
{
    private const MESSAGE = 'The domain event <%s> has no event handler or not exists.';

    public function __construct(string $domainEvent)
    {
        $message = sprintf(self::MESSAGE, $domainEvent);
        parent::__construct($message);
    }
}