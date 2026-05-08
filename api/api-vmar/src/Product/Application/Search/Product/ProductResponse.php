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
        private int $stock,
        private array $tax,
        private array $category,
        private array $supplier,
        private int $active,
        private array $priceHistory,
        private ?string $image = null,
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
            'stock' => $this->stock,
            'tax' => $this->tax,
            'category' => $this->category,
            'supplier' => $this->supplier,
            'active' => $this->active,
            'price_history' => $this->priceHistory,
            'image' => $this->image,
        ];
    }
}