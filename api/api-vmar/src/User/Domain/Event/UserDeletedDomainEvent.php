<?php

namespace SuperVMar\User\Domain\Event;

use SuperVMar\Shared\Domain\Bus\Event\DomainEvent;

final class UserDeletedDomainEvent extends DomainEvent
{
    public function __construct(
        string $aggregateId,
        ?string $eventId = null,
        ?string $occurredOn = null
    ) {
        parent::__construct($aggregateId, $eventId, $occurredOn);
    }

    public static function eventName(): string
    {
        return 'supervmar.event.user.deleted';
    }

    public function toArray(): array
    {
        return [];
    }

    public static function fromPrimitives(
        string $aggregateId,
        array $body,
        string $eventId,
        string $occurredOn
    ): self
    {
        return new self(
            $aggregateId,
            $eventId,
            $occurredOn
        );
    }
}