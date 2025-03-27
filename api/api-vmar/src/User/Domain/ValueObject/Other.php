<?php

namespace SuperVMar\User\Domain\ValueObject;

use SuperVMar\Shared\Domain\Exception\InvalidValueException;
use SuperVMar\Shared\Domain\ValueObject\StringValueObject;

final class Other extends StringValueObject
{
    protected function validate(string $value): void
    {
        if (strlen($value) > 255) {
            throw new InvalidValueException('Other cannot be longer than 255 characters');
        }
    }
}