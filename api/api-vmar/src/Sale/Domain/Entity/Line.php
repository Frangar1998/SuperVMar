<?php

namespace SuperVMar\Sale\Domain\Entity;

use SuperVMar\Sale\Domain\ValueObject\Amount;
use SuperVMar\Sale\Domain\ValueObject\Quantity;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Domain\ValueObject\Uuid;

final class Line
{
    public function __construct(
        private readonly Id $id,
        private readonly Product $product,
        private Amount $amount,
        private Quantity $quantity
    )
    {
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function product(): Product
    {
        return $this->product;
    }

    public function amount(): Amount
    {
        return $this->amount;
    }

    public function quantity(): Quantity
    {
        return $this->quantity;
    }
    
    public function addQuantity(Quantity $quantity): void
    {
        $this->quantity = $this->quantity->add($quantity);
        $this->updateAmount();
    }

    public function subtractQuantity(Quantity $quantity): void
    {
        $this->quantity = $this->quantity->subtract($quantity);
        $this->updateAmount();
    }
    
    protected function updateAmount(): void
    {
        $this->amount = new Amount($this->quantity->value() * $this->product->priceValue());
    }

    public static function fromArray(array $data): self
    {
        return new self(
            new Id($data['id']),
            Product::fromArray([
                'id' => $data['idProduct'],
                'name' => $data['nameProduct'],
                'price' => $data['price'],
                'ean' => $data['ean'],
                'idTax' => $data['idTax'],
                'nameTax' => $data['nameTax'],
                'percent' => $data['percent'],
            ]),
            new Amount($data['amount']),
            new Quantity($data['quantity'])
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->value(),
            'product' => $this->product->toArray(),
            'amount' => $this->amount->value(),
            'quantity' => $this->quantity->value()
        ];
    }
}