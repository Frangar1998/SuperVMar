<?php

namespace SuperVMar\User\Domain\ValueObject;

use SuperVMar\Shared\Domain\Exception\InvalidValueException;
use SuperVMar\Shared\Domain\ValueObject\StringValueObject;

final class Floor extends StringValueObject
{
    protected function validate(string $value): void
    {
        if (strlen($value) > 10) {
            throw new InvalidValueException('Floor cannot be longer than 10 characters');
        }

        if (!is_numeric($value)) {
            throw new InvalidValueException('Floor must be numeric');
        }
    }
}