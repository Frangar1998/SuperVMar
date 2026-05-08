<?php

namespace SuperVMar\Sale\Domain\Event;

use SuperVMar\Shared\Domain\Bus\Event\DomainEvent;

final class ProductScannedDomainEvent extends DomainEvent
{
    public function __construct(
        string $aggregateId,
        private readonly string $idProduct,
        private readonly int $quantity,
        ?string $eventId = null,
        ?string $occurredOn = null
    ) {
        parent::__construct($aggregateId, $eventId, $occurredOn);
    }

    public static function eventName(): string
    {
        return 'supervmar.event.product.scanned';
    }

    public function toArray(): array
    {
        return [
            'idProduct' => $this->idProduct,
            'quantity' => $this->quantity,
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
            $body['idProduct'],
            $body['quantity'],
            $eventId,
            $occurredOn
        );
    }
}