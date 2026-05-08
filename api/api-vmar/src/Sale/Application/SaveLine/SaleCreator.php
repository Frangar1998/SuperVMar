<?php

namespace SuperVMar\Sale\Application\SaveLine;

use SuperVMar\Sale\Domain\Entity\Product;
use SuperVMar\Sale\Domain\Sale;
use SuperVMar\Sale\Domain\SaleRepository;
use SuperVMar\Sale\Domain\ValueObject\Quantity;
use SuperVMar\Shared\Domain\Bus\Event\QueueEventBus;
use SuperVMar\Shared\Domain\ValueObject\Id;

readonly class SaleCreator
{
    public function __construct(
        private SaleRepository $saleRepository,
        private QueueEventBus  $queueEventBus,
    )
    {
    }

    public function create(
        Id       $id,
        Product  $product,
        Quantity $quantity,
    ): void
    {
        $sale = Sale::create($id);
        $sale->addOrUpdateLine($product, $quantity);
        $sale->updateAmounts($product, $quantity);

        $this->saleRepository->insert($sale);

        $this->queueEventBus->publish(...$sale->pullDomainEvents());
    }
}