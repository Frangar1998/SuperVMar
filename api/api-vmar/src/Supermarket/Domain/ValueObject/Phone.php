<?php

namespace SuperVMar\Supermarket\Domain\ValueObject;

use SuperVMar\Shared\Domain\ValueObject\StringValueObject;

final readonly class Phone extends StringValueObject
{
    protected function validate(string $value): void
    {
        if (!preg_match('/^[0-9]{9}$/', $value)) {
            throw new \InvalidArgumentException(sprintf('Phone "%s" is not valid.', $value));
        }

        if (!is_numeric($value)) {
            throw new \InvalidArgumentException('Phone must be numeric');
        }
    }
}