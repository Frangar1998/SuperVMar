<?php

namespace SuperVMar\Product\Application\Search\Product;

use SuperVMar\Product\Domain\Service\ProductSearcher;
use SuperVMar\Shared\Domain\Bus\Query\QueryHandler;

final readonly class SearchProductQueryHandler implements QueryHandler
{
    public function __construct(
        private ProductSearcher $productSearcher
    )
    {
    }

    public function __invoke(SearchProductQuery $query): ProductResponse
    {

        $product = $this->productSearcher->searchByField($query->field(), $query->value());

        return new ProductResponse(
            $product->id()->value(),
            $product->name()->value(),
            $product->price()->value(),
            $product->ean()->value(),
            $product->stock()->value(),
            $product->tax()->toArray(),
            $product->category()->toArray(),
            $product->supplier()->toArray(),
            $product->active()->value(),
            $product->priceHistory()->toArray(),
            $product->image()?->value()
        );
    }
}