<?php

namespace SuperVMar\ProductAllocation\Domain\Entity;

use SuperVMar\ProductAllocation\Domain\ValueObject\Image;
use SuperVMar\ProductAllocation\Domain\ValueObject\Stock;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Domain\ValueObject\Name;

final readonly class Product
{
    public function __construct(
        private Id     $id,
        private Name   $name,
        private Stock  $stock,
        private Image $image = new Image(''),
    )
    {
    }
    
    public function id(): Id
    {
        return $this->id;
    }

    public function name(): Name
    {
        return $this->name;
    }

    public function stock(): Stock
    {
        return $this->stock;
    }

    public function image(): Image
    {
        return $this->image;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            new Id($data['id']),
            new Name($data['name']),
            new Stock($data['stock']),
            new Image($data['image'])
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->value(),
            'name' => $this->name->value(),
            'stock' => $this->stock->value(),
            'image' => $this->image?->value()
        ];
    }

    public function equals(Product $product): bool
    {
        return $this->id->equals($product->id());
    }
    
}