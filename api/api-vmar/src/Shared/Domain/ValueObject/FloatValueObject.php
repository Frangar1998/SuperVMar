<?php

namespace SuperVMar\Shared\Domain\ValueObject;

abstract readonly class FloatValueObject
{
    protected float $value;
    public function __construct(float $value = 0)
    {
        $this->value = round($value, 2);
    }

    public function value(): float
    {
        return $this->value;
    }

    public function equals(self $otherValue): bool
    {
        return $this->value === $otherValue->value();
    }

    public function __toString(): string
    {
        return $this->value;
    }
}