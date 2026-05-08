<?php

namespace SuperVMar\ProductAllocation\Application\SubtractQuantity;

use SuperVMar\ProductAllocation\Domain\ValueObject\Quantity;
use SuperVMar\Sale\Domain\Event\ProductScannedDomainEvent;
use SuperVMar\Shared\Domain\Bus\Event\DomainEvent;
use SuperVMar\Shared\Domain\Bus\Event\DomainEventSubscriber;
use SuperVMar\Shared\Domain\ValueObject\Id;

final readonly class SubtractQuantityOnProductScanned implements DomainEventSubscriber
{
    private const string QUEUE_NAME = 'supervmar.event.product.scanned';

    public function __construct(
        private ProductAllocationQuantitySubtracter $quantitySubtracter,
    )
    {
    }

    public function __invoke(DomainEvent $event): void
    {
        $body = $event->toArray();
        $idProduct = new Id($body['idProduct']);
        $quantity = new Quantity($body['quantity']);

        $this->quantitySubtracter->subtractQuantity($idProduct, $quantity);
    }

    public static function subscribedTo(): array
    {
        return [ProductScannedDomainEvent::class];
    }

    public static function queue(): string
    {
        return self::QUEUE_NAME;
    }
}