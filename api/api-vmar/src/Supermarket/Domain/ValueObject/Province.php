<?php

namespace SuperVMar\Supermarket\Domain\ValueObject;

use SuperVMar\Shared\Domain\Exception\InvalidValueException;
use SuperVMar\Shared\Domain\ValueObject\StringValueObject;

final class Province extends StringValueObject
{
    protected function validate(string $value): void
    {
        if (empty($value)) {
            throw new InvalidValueException('Province cannot be empty');
        }

        if (strlen($value) > 100) {
            throw new InvalidValueException('Province cannot be longer than 100 characters');
        }
    }
}