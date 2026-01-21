<?php

namespace SuperVMar\Shared\Domain\ValueObject;

use SuperVMar\Shared\Domain\Exception\InvalidValueException;

abstract class StringValueObject
{

    public function __construct(protected string $value)
    {
        $this->validate($value);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    protected function validate(string $value): void {}

    final public function equals(self $otherValue): bool
    {
        return $this->value === $otherValue->value();
    }

    final public function value(): string
    {
        return $this->value;
    }
    
}