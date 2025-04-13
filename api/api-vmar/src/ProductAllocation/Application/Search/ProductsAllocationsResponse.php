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
        $response = [];
        foreach ($this->productsAllocations as $productAllocation) {
            $response[$productAllocation['space']['id']] = $productAllocation;
        }
        return $response;
    }
}