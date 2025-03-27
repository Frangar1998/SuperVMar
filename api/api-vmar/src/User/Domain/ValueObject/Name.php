<?php

namespace SuperVMar\User\Domain\ValueObject;

use SuperVMar\Shared\Domain\Exception\InvalidValueException;
use SuperVMar\Shared\Domain\Utils;
use SuperVMar\Shared\Domain\ValueObject\StringValueObject;

final class Name extends StringValueObject
{
    public function __construct(string $value)
    {
        parent::__construct(Utils::toTitleCase($value));
    }

    protected function validate(string $value): void
    {
        if (empty($value)) {
            throw new InvalidValueException('Name cannot be empty');
        }

        if (strlen($value) > 100) {
            throw new InvalidValueException('Name cannot be longer than 100 characters');
        }
    }
}