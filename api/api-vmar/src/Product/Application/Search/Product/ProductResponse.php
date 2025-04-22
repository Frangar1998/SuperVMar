<?php

namespace SuperVMar\Product\Application\Search\Product;

use SuperVMar\Shared\Domain\Bus\Query\Response;

final readonly class ProductResponse implements Response
{
    public function __construct(
        private string $id,
        private string $name,
        private float $price,
        private string $ean,
        private array $tax
    )
    {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'ean' => $this->ean,
            'tax' => $this->tax
        ];
    }
}