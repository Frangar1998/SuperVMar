<?php

namespace SuperVMar\Product\Domain;

use SuperVMar\Product\Domain\Entity\Category;
use SuperVMar\Product\Domain\Entity\PriceHistory;
use SuperVMar\Product\Domain\Entity\Supplier;
use SuperVMar\Product\Domain\Entity\Tax;
use SuperVMar\Product\Domain\ValueObject\Active;
use SuperVMar\Product\Domain\ValueObject\Ean;
use SuperVMar\Product\Domain\ValueObject\Image;
use SuperVMar\Product\Domain\ValueObject\Price;
use SuperVMar\Product\Domain\ValueObject\Stock;
use SuperVMar\Shared\Domain\AggregateRoot;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Domain\ValueObject\Name;

final class Product extends AggregateRoot
{
    public function __construct(
        private readonly Id       $id,
        private Name              $name,
        private Price             $price,
        private readonly Ean      $ean,
        private Stock             $stock,
        private Tax               $tax,
        private Category          $category,
        private readonly Supplier $supplier,
        private Active            $active,
        private PriceHistory      $priceHistory,
        private ?Image            $image = null,
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

    public function price(): Price
    {
        return $this->price;
    }

    public function ean(): Ean
    {
        return $this->ean;
    }

    public function stock(): Stock
    {
        return $this->stock;
    }

    public function tax(): Tax
    {
        return $this->tax;
    }

    public function category(): Category
    {
        return $this->category;
    }

    public function supplier(): Supplier
    {
        return $this->supplier;
    }

    public function active(): Active
    {
        return $this->active;
    }

    public function priceHistory(): PriceHistory
    {
        return $this->priceHistory;
    }

    public function image(): ?Image
    {
        return $this->image;
    }

    public static function create(
        Id $id,
        Name $name,
        Price $price,
        Ean $ean,
        Stock $stock,
        Tax $tax,
        Category $category,
        Supplier $supplier,
        Active $active,
        PriceHistory $priceHistory,
        ?Image $image = null
    ): self
    {
        return new self(
            $id,
            $name,
            $price,
            $ean,
            $stock,
            $tax,
            $category,
            $supplier,
            $active,
            $priceHistory,
            $image
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            new Id($data['id']),
            new Name($data['name']),
            new Price($data['price']),
            new Ean($data['ean']),
            new Stock($data['stock']),
            Tax::fromArray($data['tax']),
            Category::fromArray($data['category']),
            Supplier::fromArray($data['supplier']),
            new Active($data['active']),
            PriceHistory::fromArray($data['price_history']),
            isset($data['image']) ? new Image($data['image']) : null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->value(),
            'name' => $this->name->value(),
            'price' => $this->price->value(),
            'ean' => $this->ean->value(),
            'stock' => $this->stock->value(),
            'tax' => $this->tax->toArray(),
            'category' => $this->category->toArray(),
            'supplier' => $this->supplier->toArray(),
            'active' => $this->active->value(),
            'price_history' => $this->priceHistory->toArray(),
            'image' => $this->image?->value()
        ];
    }
    
    
}