<?php

namespace SuperVMar\Sale\Domain\ValueObject;

use SuperVMar\Shared\Domain\ValueObject\FloatValueObject;

readonly class Amount extends FloatValueObject
{
    public function add(self $addedValue): static
    {
        return new static($this->value + $addedValue->value());
    }

    public function subtract(self $subtractedValue): static
    {
        return new static($this->value - $subtractedValue->value());
    }
}