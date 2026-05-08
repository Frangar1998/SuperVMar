<?php

namespace SuperVMar\Sale\Domain\ValueObject;

use SuperVMar\Shared\Domain\Exception\InvalidValueException;
use SuperVMar\Shared\Domain\ValueObject\FloatValueObject;

final readonly class Price extends FloatValueObject
{
    public function __construct(float $value)
    {
        $this->validate($value);
        parent::__construct($value);
    }

    protected function validate(float $value): void
    {
        if ($value < 0) {
            throw new InvalidValueException('Price must be positive float.');
        }
    }
}