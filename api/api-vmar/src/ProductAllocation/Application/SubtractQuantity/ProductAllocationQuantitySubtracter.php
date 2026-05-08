<?php

namespace SuperVMar\ProductAllocation\Application\SubtractQuantity;

use SuperVMar\ProductAllocation\Domain\Entity\Product;
use SuperVMar\ProductAllocation\Domain\Entity\Space;
use SuperVMar\ProductAllocation\Domain\Event\LowStockDetectedDomainEvent;
use SuperVMar\ProductAllocation\Domain\ProductAllocationRepository;
use SuperVMar\ProductAllocation\Domain\Service\ProductAllocationSearcher;
use SuperVMar\ProductAllocation\Domain\ValueObject\Quantity;
use SuperVMar\Shared\Domain\Bus\Event\QueueEventBus;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;

final readonly class ProductAllocationQuantitySubtracter
{
    public function __construct(
        private ProductAllocationSearcher   $productAllocationSearcher,
        private ProductAllocationRepository $productAllocationRepository,
        private QueueEventBus               $queueEventBus,
    )
    {
    }

    /**
     * @throws ItemNotFoundException
     */
    public function subtractQuantity(
        Id $idProduct,
        Quantity $quantity,
    ): void
    {
        $productAllocation = $this->productAllocationSearcher->searchFirstAvailableByProduct($idProduct);
        $productAllocation->subtractQuantity($quantity);
        $this->productAllocationRepository->update($productAllocation);

        if ($productAllocation->quantity()->value() < 3) {
            $space = $productAllocation->space();
            $this->queueEventBus->publish(
                new LowStockDetectedDomainEvent(
                    $space->id()->value(),
                    $productAllocation->product()->id()->value(),
                    $productAllocation->product()->name()->value(),
                    $space->id()->value(),
                    json_encode($space->position()->toArray()),
                    $space->zone()->id()->value(),
                    $space->zone()->name()->value(),
                    $productAllocation->quantity()->value(),
                    $space->maxSpots()->value(),
                )
            );
        }
    }
}