<?php

namespace SuperVMar\ProductAllocation\Application\Save;

use SuperVMar\ProductAllocation\Domain\Entity\Product;
use SuperVMar\ProductAllocation\Domain\Entity\Space;
use SuperVMar\ProductAllocation\Domain\ProductAllocation;
use SuperVMar\ProductAllocation\Domain\ProductAllocationRepository;
use SuperVMar\ProductAllocation\Domain\ValueObject\Quantity;
use SuperVMar\Shared\Domain\ValueObject\Id;

final readonly class ProductAllocationCreator
{
    public function __construct(
        private ProductAllocationRepository $productAllocationRepository,
    )
    {
    }

    public function create(
        Product  $product,
        Space    $space,
        Quantity $quantity,
    ): void
    {
        $this->productAllocationRepository->insert(
            ProductAllocation::create(
                $product,
                $space,
                $quantity,
            )
        );

    }
}