<?php

namespace SuperVMar\Product\Domain\ValueObject;

use SuperVMar\Shared\Domain\Exception\InvalidValueException;
use SuperVMar\Shared\Domain\ValueObject\IntValueObject;

final readonly class Active extends IntValueObject
{
    public function __construct(int $value)
    {
        $this->validate($value);
        parent::__construct($value);
    }

    protected function validate(int $value): void
    {
        if ($value !== 0 && $value !== 1) {
            throw new InvalidValueException('Product active must be 0 or 1.');
        }
    }
}