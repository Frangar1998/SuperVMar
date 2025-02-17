<?php

namespace SuperVMar\Shared\Domain\ValueObject;

abstract readonly class StringValueObject
{

    public function __construct(protected string $value)
    {
        $this->validate($value);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    protected function validate(string $value): void
    {
        if (empty($value)) {
            throw new \InvalidArgumentException('The string value cannot be empty');
        }
    }

    final public function equals(self $otherValue): bool
    {
        return $this->value === $otherValue->value();
    }

    final public function value(): string
    {
        return $this->value;
    }
    
}