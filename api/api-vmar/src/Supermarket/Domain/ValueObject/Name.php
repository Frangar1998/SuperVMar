<?php

namespace SuperVMar\Supermarket\Domain\ValueObject;

use SuperVMar\Shared\Domain\Exception\InvalidValueException;
use SuperVMar\Shared\Domain\ValueObject\StringValueObject;

final class Name extends StringValueObject
{
    protected function validate(string $value): void
    {
        if (empty($value)) {
            throw new InvalidValueException('Name cannot be empty');
        }

        if (strlen($value) > 255) {
            throw new InvalidValueException('Name cannot be longer than 255 characters');
        }
    }
}