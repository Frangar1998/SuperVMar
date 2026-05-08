<?php

namespace SuperVMar\Supplier\Domain\ValueObject;

use SuperVMar\Shared\Domain\Exception\InvalidValueException;
use SuperVMar\Shared\Domain\ValueObject\StringValueObject;

final class Contact extends StringValueObject
{
    public function __construct(string $value)
    {
        parent::__construct($value);
    }

    protected function validate(string $value): void
    {
        if (strlen($value) > 100) {
            throw new InvalidValueException('Contact cannot be longer than 255 characters');
        }
    }
}