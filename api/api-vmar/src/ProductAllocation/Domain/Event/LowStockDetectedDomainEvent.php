<?php

namespace SuperVMar\ProductAllocation\Domain\Event;

use SuperVMar\Shared\Domain\Bus\Event\DomainEvent;

final class LowStockDetectedDomainEvent extends DomainEvent
{
    public function __construct(
        string $aggregateId,
        private readonly string $idProduct,
        private readonly string $productName,
        private readonly string $idSpace,
        private readonly string $spacePosition,
        private readonly string $idZone,
        private readonly string $zoneName,
        private readonly int $quantity,
        private readonly int $maxSpots,
        ?string $eventId = null,
        ?string $occurredOn = null
    ) {
        parent::__construct($aggregateId, $eventId, $occurredOn);
    }

    public static function eventName(): string
    {
        return 'supervmar.event.product_allocation.low_stock';
    }

    public function toArray(): array
    {
        return [
            'idProduct' => $this->idProduct,
            'productName' => $this->productName,
            'idSpace' => $this->idSpace,
            'spacePosition' => $this->spacePosition,
            'idZone' => $this->idZone,
            'zoneName' => $this->zoneName,
            'quantity' => $this->quantity,
            'maxSpots' => $this->maxSpots,
        ];
    }

    public static function fromPrimitives(
        string $aggregateId,
        array $body,
        string $eventId,
        string $occurredOn
    ): self {
        return new self(
            $aggregateId,
            $body['idProduct'],
            $body['productName'],
            $body['idSpace'],
            $body['spacePosition'],
            $body['idZone'],
            $body['zoneName'],
            $body['quantity'],
            $body['maxSpots'],
            $eventId,
            $occurredOn
        );
    }
}
