<?php

namespace SuperVMar\Supermarket\Domain\ValueObject;

use SuperVMar\Shared\Domain\ValueObject\StringValueObject;

final readonly class City extends StringValueObject
{
    protected function validate(string $value): void
    {
        if (empty($value)) {
            throw new \InvalidArgumentException('City cannot be empty');
        }

        if (strlen($value) > 100) {
            throw new \InvalidArgumentException('City cannot be longer than 100 characters');
        }
    }
}