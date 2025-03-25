<?php

namespace SuperVMar\Supermarket\Domain\ValueObject;

use SuperVMar\Shared\Domain\Exception\InvalidValueException;
use SuperVMar\Shared\Domain\ValueObject\StringValueObject;

final class Phone extends StringValueObject
{
    protected function validate(string $value): void
    {
        if (!preg_match('/^[0-9]{9}$/', $value)) {
            throw new InvalidValueException(sprintf('Phone "%s" is not valid.', $value));
        }

        if (!is_numeric($value)) {
            throw new InvalidValueException('Phone must be numeric');
        }
    }
}