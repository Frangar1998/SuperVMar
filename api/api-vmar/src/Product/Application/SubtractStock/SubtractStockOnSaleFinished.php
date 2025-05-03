<?php

namespace SuperVMar\Product\Application\SubtractStock;

use SuperVMar\Product\Domain\ValueObject\Stock;
use SuperVMar\Sale\Domain\Event\SaleFinishedDomainEvent;
use SuperVMar\Shared\Domain\Bus\Event\DomainEvent;
use SuperVMar\Shared\Domain\Bus\Event\DomainEventSubscriber;
use SuperVMar\Shared\Domain\ValueObject\Id;

final readonly class SubtractStockOnSaleFinished implements DomainEventSubscriber
{
    private const string QUEUE_NAME = 'supervmar.event.sale.finished';

    public function __construct(
        private ProductStockSubtracter $productStockSubtracter,
    )
    {
    }

    public function __invoke(DomainEvent $event): void
    {
        $body = $event->toArray();

        foreach ($body['lines'] as $line) {
            $idProduct = new Id($line['product']['id']);
            $stock = new Stock($line['quantity']);
            $this->productStockSubtracter->subtractStock($idProduct, $stock);
        }

    }

    public static function subscribedTo(): array
    {
        return [SaleFinishedDomainEvent::class];
    }

    public static function queue(): string
    {
        return self::QUEUE_NAME;
    }
}