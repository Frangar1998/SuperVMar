<?php

namespace SuperVMar\ProductAllocation\Domain;

use SuperVMar\ProductAllocation\Domain\Entity\Product;
use SuperVMar\ProductAllocation\Domain\Entity\Space;
use SuperVMar\ProductAllocation\Domain\ValueObject\Quantity;
use SuperVMar\Shared\Domain\AggregateRoot;
use SuperVMar\Shared\Domain\Utils;
use SuperVMar\Shared\Domain\ValueObject\Id;

final class ProductAllocation extends AggregateRoot
{
    public function __construct(
        private Product $product,
        private readonly Space   $space,
        private Quantity         $quantity,
    )
    {
    }

    public function product(): Product
    {
        return $this->product;
    }

    public function space(): Space
    {
        return $this->space;
    }

    public function quantity(): Quantity
    {
        return $this->quantity;
    }

    public function changeQuantity(Quantity $quantity): void
    {
        if (!$this->quantity->equals($quantity)) {
            $quantity->validate($this->space->maxSpots()->value());
            $this->quantity = $quantity;
        }
    }

    public function changeProduct(Product $product): void
    {
        if (!$this->product->equals($product)) {
            $this->product = $product;
        }
    }

    public static function create(
        Product  $product,
        Space    $space,
        Quantity $quantity
    ): self
    {
        return new self(
            $product,
            $space,
            $quantity
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            Product::fromArray([
                'id' => $data['idProduct'],
                'name' => $data['nameProduct'],
                'stock' => $data['stock'],
                'image' => $data['image'],
            ]),
            Space::fromArray([
                'id' => $data['idSpace'],
                'position' => Utils::jsonDecode($data['position']),
                'maxSpots' => $data['maxSpots'],
                'zone' => [
                    'id' => $data['idZone'],
                    'name' => $data['nameZone'],
                    'cornerTopLeft' => Utils::jsonDecode($data['cornerTopLeft']),
                    'cornerTopRight' => Utils::jsonDecode($data['cornerTopRight']),
                    'cornerBottomRight' => Utils::jsonDecode($data['cornerBottomRight']),
                    'cornerBottomLeft' => Utils::jsonDecode($data['cornerBottomLeft']),
                ]
            ]),
            new Quantity($data['quantity'])
        );
    }

    public function toArray(): array
    {
        return [
            'product' => $this->product->toArray(),
            'space' => $this->space->toArray(),
            'quantity' => $this->quantity->value()
        ];
    }
}