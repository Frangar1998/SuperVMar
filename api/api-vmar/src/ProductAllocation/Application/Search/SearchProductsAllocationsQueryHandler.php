<?php

namespace SuperVMar\ProductAllocation\Application\Search;

use SuperVMar\ProductAllocation\Domain\Service\ProductAllocationSearcher;
use SuperVMar\Shared\Domain\Bus\Query\QueryHandler;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;

final readonly class SearchProductsAllocationsQueryHandler implements QueryHandler
{
    public function __construct(
        private ProductAllocationSearcher $productAllocationSearcher
    )
    {
    }

    public function __invoke(SearchProductsAllocationsQuery $query): ProductsAllocationsResponse
    {
        try {
            $productsAllocations = $this->productAllocationSearcher->searchAll();
        } catch (ItemNotFoundException) {
            return new ProductsAllocationsResponse([]);
        }

        return new ProductsAllocationsResponse(
            $productsAllocations->toArray(),
        );
    }
}