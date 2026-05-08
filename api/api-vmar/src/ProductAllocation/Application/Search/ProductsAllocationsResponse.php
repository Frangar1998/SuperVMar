<?php

namespace SuperVMar\ProductAllocation\Application\Search;

use SuperVMar\Shared\Domain\Bus\Query\Response;

final readonly class ProductsAllocationsResponse implements Response
{
    public function __construct(
        private array $productsAllocations
    )
    {
    }

    public function toArray(): array
    {
        return $this->productsAllocations;
    }
}