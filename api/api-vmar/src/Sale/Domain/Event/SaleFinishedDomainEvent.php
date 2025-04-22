<?php

namespace SuperVMar\Sale\Domain\Event;

use SuperVMar\Shared\Domain\Bus\Event\DomainEvent;

final class SaleFinishedDomainEvent extends DomainEvent
{
    public function __construct(
        string $aggregateId,
        private readonly array $lines,
        ?string $eventId = null,
        ?string $occurredOn = null
    ) {
        parent::__construct($aggregateId, $eventId, $occurredOn);
    }

    public static function eventName(): string
    {
        return 'supervmar.event.sale.finished';
    }

    public function toArray(): array
    {
        return [
            'lines' => $this->lines,
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
            $body['lines'],
            $eventId,
            $occurredOn
        );
    }
}