<?php

namespace SuperVMar\User\Domain\ValueObject;

use SuperVMar\Shared\Domain\Exception\InvalidValueException;
use SuperVMar\Shared\Domain\ValueObject\StringValueObject;

final class Username extends StringValueObject
{
    protected function validate(string $value): void
    {
        if (empty($value)) {
            throw new InvalidValueException('Username cannot be empty');
        }

        if (strlen($value) > 100) {
            throw new InvalidValueException('Username cannot be longer than 100 characters');
        }
    }
}