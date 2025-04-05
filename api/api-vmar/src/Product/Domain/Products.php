<?php

namespace SuperVMar\Product\Domain;

use SuperVMar\Shared\Domain\Collection;

final class Products extends Collection
{
    protected function type(): string
    {
        return Product::class;
    }

    public static function fromArray(array $products): self
    {
        return new self(
            array_map(
                fn(array $product) => Product::fromArray($product),
                $products
            )
        );
    }
}