<?php

namespace SuperVMar\ProductAllocation\Application\Delete;

use SuperVMar\ProductAllocation\Domain\ProductAllocationRepository;
use SuperVMar\Shared\Domain\ValueObject\Id;

final readonly class ProductAllocationDeleter
{
    public function __construct(
        private ProductAllocationRepository $productRepository,
    )
    {
    }

    public function delete(
        Id $idSpace
    ): void
    {
        $this->productRepository->delete($idSpace);
    }
}