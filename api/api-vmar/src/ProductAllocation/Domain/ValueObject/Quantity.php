<?php

namespace SuperVMar\ProductAllocation\Domain\ValueObject;

use SuperVMar\Shared\Domain\Exception\InvalidValueException;
use SuperVMar\Shared\Domain\ValueObject\IntValueObject;

final readonly class Quantity extends IntValueObject
{
    public function validate(int $maxSpots): void
    {
        if ($this->value() > $maxSpots) {
            throw new InvalidValueException("Product quantity for space cannot get over his max spots.");
        }
    }

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