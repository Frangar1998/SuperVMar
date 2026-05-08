<?php

namespace SuperVMar\Notification\Application\Send;

use SuperVMar\ProductAllocation\Domain\Event\LowStockDetectedDomainEvent;
use SuperVMar\Shared\Domain\Bus\Event\DomainEvent;
use SuperVMar\Shared\Domain\Bus\Event\DomainEventSubscriber;

final readonly class NotifyRestockersOnLowStock implements DomainEventSubscriber
{
    private const string QUEUE_NAME = 'supervmar.event.product_allocation.low_stock';

    public function __construct(
        private RestockerNotifier $restockerNotifier,
    )
    {
    }

    public function __invoke(DomainEvent $event): void
    {
        $body = $event->toArray();

        $this->restockerNotifier->notify(
            $body['idZone'],
            $body['productName'],
            $body['zoneName'],
            $body['quantity'],
        );
    }

    public static function subscribedTo(): array
    {
        return [LowStockDetectedDomainEvent::class];
    }

    public static function queue(): string
    {
        return self::QUEUE_NAME;
    }
}
