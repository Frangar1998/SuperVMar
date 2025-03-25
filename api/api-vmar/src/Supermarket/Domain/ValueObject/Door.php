<?php

namespace SuperVMar\Supermarket\Domain\ValueObject;

use SuperVMar\Shared\Domain\Exception\InvalidValueException;
use SuperVMar\Shared\Domain\ValueObject\StringValueObject;

final class Door extends StringValueObject
{
    protected function validate(string $value): void
    {
        if (empty($value)) {
            throw new InvalidValueException('Door cannot be empty');
        }

        if (strlen($value) > 10) {
            throw new InvalidValueException('Door cannot be longer than 10 characters');
        }
    }
}