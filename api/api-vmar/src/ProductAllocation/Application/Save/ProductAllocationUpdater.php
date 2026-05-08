<?php

namespace SuperVMar\ProductAllocation\Application\Save;

use SuperVMar\ProductAllocation\Domain\Entity\Product;
use SuperVMar\ProductAllocation\Domain\Entity\Space;
use SuperVMar\ProductAllocation\Domain\ProductAllocationRepository;
use SuperVMar\ProductAllocation\Domain\Service\ProductAllocationSearcher;
use SuperVMar\ProductAllocation\Domain\ValueObject\Quantity;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;

readonly class ProductAllocationUpdater
{
    public function __construct(
        private ProductAllocationSearcher   $productAllocationSearcher,
        private ProductAllocationRepository $productAllocationRepository
    )
    {
    }

    /**
     * @throws ItemNotFoundException
     */
    public function update(
        Product  $product,
        Space    $space,
        Quantity $quantity,
    ): void
    {
        $productAllocation = $this->productAllocationSearcher->searchBySpace($space->id());
        $productAllocation->changeQuantity($quantity);
        $productAllocation->changeProduct($product);
        $this->productAllocationRepository->update($productAllocation);
    }
}