<?php

namespace SuperVMar\Supermarket\Domain\ValueObject;

use SuperVMar\Shared\Domain\ValueObject\StringValueObject;

final readonly class Province extends StringValueObject
{
    protected function validate(string $value): void
    {
        if (empty($value)) {
            throw new \InvalidArgumentException('Province cannot be empty');
        }

        if (strlen($value) > 100) {
            throw new \InvalidArgumentException('Province cannot be longer than 100 characters');
        }
    }
}