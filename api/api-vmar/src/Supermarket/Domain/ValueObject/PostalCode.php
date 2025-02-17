<?php

namespace SuperVMar\Supermarket\Domain\ValueObject;

use SuperVMar\Shared\Domain\ValueObject\StringValueObject;

final readonly class PostalCode extends StringValueObject
{
    protected function validate(string $value): void
    {
        if (!preg_match('/^[0-9]{5}$/', $value)) {
            throw new \InvalidArgumentException(sprintf('Postal code "%s" is not valid.', $value));
        }

        if (!is_numeric($value)) {
            throw new \InvalidArgumentException('Postal code must be numeric');
        }
    }
}