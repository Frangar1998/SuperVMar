<?php

namespace SuperVMar\Sale\Domain\Entity;

use SuperVMar\Sale\Domain\ValueObject\Ean;
use SuperVMar\Sale\Domain\ValueObject\Price;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Domain\ValueObject\Name;

final readonly class Product
{
    public function __construct(
        private Id    $id,
        private Name  $name,
        private Price $price,
        private Ean   $ean,
        private Tax   $tax,
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

    public function tax(): Tax
    {
        return $this->tax;
    }

    public function priceValue(): float
    {
        return $this->price->value();
    }

    public function taxValue(): float
    {
        return $this->tax->taxValue();
    }

    public function taxInvertedValue(): float
    {
        return $this->tax->taxInvertedValue();
    }

    public static function fromArray(array $data): self
    {
        return new self(
            new Id($data['id']),
            new Name($data['name']),
            new Price($data['price']),
            new Ean($data['ean']),
            Tax::fromArray([
                'id' => $data['idTax'],
                'name' => $data['nameTax'],
                'percent' => $data['percent'],
            ])
        );
    }

    public static function fromPrimitives(
        string $id,
        string $name,
        float $price,
        string $ean,
        array $tax
    ): self
    {
        return new self(
            new Id($id),
            new Name($name),
            new Price($price),
            new Ean($ean),
            Tax::fromArray([
                'id' => $tax['id'],
                'name' => $tax['name'],
                'percent' => $tax['percent'],
            ])
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->value(),
            'name' => $this->name->value(),
            'price' => $this->price->value(),
            'ean' => $this->ean->value(),
            'tax' => $this->tax->toArray(),
        ];
    }

    public function equals(self $other): bool
    {
        return $this->id->equals($other->id());
    }
    
}