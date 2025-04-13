<?php

namespace SuperVMar\ProductAllocation\Application\Search;

use SuperVMar\ProductAllocation\Domain\Service\ProductAllocationSearcher;
use SuperVMar\Shared\Domain\Bus\Query\QueryHandler;

final readonly class SearchProductsAllocationsQueryHandler implements QueryHandler
{
    public function __construct(
        private ProductAllocationSearcher $productAllocationSearcher
    )
    {
    }

    public function __invoke(SearchProductsAllocationsQuery $query): ProductsAllocationsResponse
    {

        $productsAllocations = $this->productAllocationSearcher->searchAll();

        return new ProductsAllocationsResponse(
            $productsAllocations->toArray(),
        );
    }
}