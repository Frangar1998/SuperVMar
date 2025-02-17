<?php

namespace SuperVMar\Shared\Domain\Bus\Event;

use DateTime;
use SuperVMar\Shared\Domain\ValueObject\Uuid;

abstract class DomainEvent
{
    public function __construct(
        private readonly string $aggregateId,
        private ?string $eventId = null,
        private ?string $occurredOn = null)
    {
        $this->eventId = $eventId ?? Uuid::random()->value();
        $this->occurredOn = $occurredOn ?? (new DateTime())->format('Y-m-d H:i:s');
    }

    final public function aggregateId(): string
    {
        return $this->aggregateId;
    }

    final public function eventId(): string
    {
        return $this->eventId;
    }

    final public function occurredOn(): string
    {
        return $this->occurredOn;
    }

    abstract public static function eventName(): string;

    abstract public function toPrimitives(): array;

    abstract public static function fromPrimitives(
        string $aggregateId,
        string $eventId,
        string $occurredOn,
        array $body
    ): self;
}