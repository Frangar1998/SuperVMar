<?php

namespace SuperVMar\Supermarket\Domain\ValueObject;

use SuperVMar\Shared\Domain\Exception\InvalidValueException;
use SuperVMar\Shared\Domain\ValueObject\IntValueObject;

final readonly class Point extends IntValueObject
{
    public function __construct(int $value)
    {
        $this->validate($value);
        parent::__construct($value);
    }

    protected function validate(int $value): void
    {
        if ($value < 0) {
            throw new InvalidValueException('Points in coordinates must be positive integer.');
        }
    }
}