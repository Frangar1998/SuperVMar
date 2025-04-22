<?php

namespace SuperVMar\Sale\Domain\ValueObject;

use SuperVMar\Shared\Domain\Exception\InvalidValueException;
use SuperVMar\Shared\Domain\ValueObject\StringValueObject;

final class Ean extends StringValueObject
{
    public function __construct(string $value)
    {
        parent::__construct($value);
    }

    protected function validate(string $value): void
    {
        if (empty($value)) {
            throw new InvalidValueException('Ean cannot be empty');
        }

        if (!is_numeric($value)) {
            throw new InvalidValueException('Ean must be numeric');
        }

        if (strlen($value) > 13) {
            throw new InvalidValueException('Ean cannot be longer than 13 characters');
        }
    }
}