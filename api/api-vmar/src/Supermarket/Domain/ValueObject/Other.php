<?php

namespace SuperVMar\Supermarket\Domain\ValueObject;

use SuperVMar\Shared\Domain\ValueObject\StringValueObject;

final readonly class Other extends StringValueObject
{
    protected function validate(string $value): void
    {
        if (empty($value)) {
            throw new \InvalidArgumentException('Other cannot be empty');
        }

        if (strlen($value) > 255) {
            throw new \InvalidArgumentException('Other cannot be longer than 255 characters');
        }
    }
}