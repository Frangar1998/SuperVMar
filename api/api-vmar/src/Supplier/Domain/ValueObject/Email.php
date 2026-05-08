<?php

namespace SuperVMar\Supplier\Domain\ValueObject;

use SuperVMar\Shared\Domain\Exception\InvalidValueException;
use SuperVMar\Shared\Domain\ValueObject\StringValueObject;

final class Email extends StringValueObject
{
    protected function validate(string $value): void
    {
        if (!filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidValueException($this->value);
        }

        if (strlen($value) > 100) {
            throw new InvalidValueException('Email cannot be longer than 100 characters');
        }
    }
}