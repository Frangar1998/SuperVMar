<?php

namespace SuperVMar\ProductAllocation\Domain\ValueObject;

use SuperVMar\Shared\Domain\Exception\InvalidValueException;
use SuperVMar\Shared\Domain\ValueObject\IntValueObject;

final readonly class Spots extends IntValueObject
{
    public function __construct(int $value)
    {
        $this->validate($value);
        parent::__construct($value);
    }

    protected function validate(int $value): void
    {
        if ($value < 0) {
            throw new InvalidValueException('Spots in spaces must be positive integer.');
        }
    }
}