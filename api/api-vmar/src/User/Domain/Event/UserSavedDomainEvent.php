<?php

namespace SuperVMar\User\Domain\Event;

use SuperVMar\Shared\Domain\Bus\Event\DomainEvent;

final class UserSavedDomainEvent extends DomainEvent
{
    public function __construct(
        string $aggregateId,
        private readonly array $allocations,
        ?string $eventId = null,
        ?string $occurredOn = null
    ) {
        parent::__construct($aggregateId, $eventId, $occurredOn);
    }

    public static function eventName(): string
    {
        return 'supervmar.event.user.saved';
    }

    public function toArray(): array
    {
        return [
            'allocations' => $this->allocations,
        ];
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
            $body['allocations'],
            $eventId,
            $occurredOn
        );
    }
}