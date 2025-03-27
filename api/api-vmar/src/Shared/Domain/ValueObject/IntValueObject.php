<?php

namespace SuperVMar\Shared\Domain\ValueObject;

abstract readonly class IntValueObject
{
    public function __construct(protected int $value)
    {}

    public function value(): int
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