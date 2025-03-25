<?php

namespace SuperVMar\Supermarket\Domain\ValueObject;

use SuperVMar\Shared\Domain\Exception\InvalidValueException;
use SuperVMar\Shared\Domain\ValueObject\StringValueObject;

final class Number extends StringValueObject
{
    protected function validate(string $value): void
    {
        if (empty($value)) {
            throw new InvalidValueException('Number cannot be empty');
        }

        if (strlen($value) > 10) {
            throw new InvalidValueException('Number cannot be longer than 10 digits');
        }

        if (!is_numeric($value)) {
            throw new InvalidValueException('Number must be numeric');
        }
    }
}
