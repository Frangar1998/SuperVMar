<?php

namespace SuperVMar\Product\Application\Search\Products;

use SuperVMar\Shared\Domain\Bus\Query\Response;

final readonly class ProductsResponse implements Response
{
    public function __construct(
        private array $products,
    )
    {
    }

    public function toArray(): array
    {
        return [
            'products' => $this->products,
        ];
    }
}