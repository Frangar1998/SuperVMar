<?php

namespace SuperVMar\Supermarket\Domain\ValueObject;

use SuperVMar\Shared\Domain\ValueObject\StringValueObject;

final readonly class Floor extends StringValueObject
{
    protected function validate(string $value): void
    {
        if (empty($value)) {
            throw new \InvalidArgumentException('Floor cannot be empty');
        }

        if (strlen($value) > 10) {
            throw new \InvalidArgumentException('Floor cannot be longer than 100 characters');
        }

        if (!is_numeric($value)) {
            throw new \InvalidArgumentException('Floor must be numeric');
        }
    }
}