<?php

namespace SuperVMar\Supermarket\Domain\ValueObject;

use SuperVMar\Shared\Domain\Exception\InvalidValueException;
use SuperVMar\Shared\Domain\ValueObject\StringValueObject;

final class City extends StringValueObject
{
    protected function validate(string $value): void
    {
        if (empty($value)) {
            throw new InvalidValueException('City cannot be empty');
        }

        if (strlen($value) > 100) {
            throw new InvalidValueException('City cannot be longer than 100 characters');
        }
    }
}