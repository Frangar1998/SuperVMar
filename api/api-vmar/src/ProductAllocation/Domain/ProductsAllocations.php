<?php

namespace SuperVMar\ProductAllocation\Domain;

use SuperVMar\Shared\Domain\Collection;

final class ProductsAllocations extends Collection
{
    protected function type(): string
    {
        return ProductAllocation::class;
    }

    public static function fromArray(array $productsAllocations): self
    {
        return new self(
            array_map(
                fn(array $productAllocation) => ProductAllocation::fromArray($productAllocation),
                $productsAllocations
            )
        );
    }
}