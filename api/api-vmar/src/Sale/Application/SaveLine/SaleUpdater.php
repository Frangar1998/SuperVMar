<?php

namespace SuperVMar\Sale\Application\SaveLine;

use SuperVMar\Sale\Domain\Entity\Product;
use SuperVMar\Sale\Domain\SaleRepository;
use SuperVMar\Sale\Domain\Service\SaleSearcher;
use SuperVMar\Sale\Domain\ValueObject\Quantity;
use SuperVMar\Shared\Domain\Bus\Event\QueueEventBus;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;

final readonly class SaleUpdater
{
    public function __construct(
        private SaleSearcher   $saleSearcher,
        private SaleRepository $saleRepository,
        private QueueEventBus  $queueEventBus,
    )
    {
    }

    /**
     * @throws ItemNotFoundException
     */
    public function update(
        Id       $id,
        Product  $product,
        Quantity $quantity,
    ): void
    {
        $sale = $this->saleSearcher->search($id);
        $sale->addOrUpdateLine($product, $quantity);
        $sale->updateAmounts($product, $quantity);

        $this->saleRepository->update($sale);

        $this->queueEventBus->publish(...$sale->pullDomainEvents());
    }
}