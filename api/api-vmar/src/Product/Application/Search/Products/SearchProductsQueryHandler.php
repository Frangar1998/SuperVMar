<?php

namespace SuperVMar\Product\Application\Search\Products;

use SuperVMar\Product\Domain\Service\ProductSearcher;
use SuperVMar\Shared\Domain\Bus\Query\QueryHandler;

final readonly class SearchProductsQueryHandler implements QueryHandler
{
    public function __construct(
        private ProductSearcher $productSearcher
    )
    {
    }

    public function __invoke(SearchProductsQuery $query): ProductsResponse
    {

        $products = $this->productSearcher->searchAll();

        return new ProductsResponse(
            $products->toTableData(),
        );
    }
}