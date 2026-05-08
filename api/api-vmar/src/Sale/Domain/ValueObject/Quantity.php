<?php

namespace SuperVMar\Sale\Domain\ValueObject;

use SuperVMar\Shared\Domain\ValueObject\IntValueObject;

final readonly class Quantity extends IntValueObject
{
    public function add(self $addedQuantity): self
    {
        return new self($this->value + $addedQuantity->value());
    }

    public function subtract(self $subtracteQuantity): self
    {
        return new self(
            max($this->value - $subtracteQuantity->value(), 0)
        );
    }
}