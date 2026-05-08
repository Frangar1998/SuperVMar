<?php

namespace SuperVMar\Product\Domain\ValueObject;

use SuperVMar\Shared\Domain\Exception\InvalidValueException;
use SuperVMar\Shared\Domain\ValueObject\IntValueObject;

final readonly class Active extends IntValueObject
{
    public function __construct(int $value)
    {
        $this->validate($value);
        parent::__construct($value);
    }

    protected function validate(int $value): void
    {
        if ($value !== Status::INACTIVE->value && $value !== Status::ACTIVE->value) {
            throw new InvalidValueException(
                sprintf(
                    'Product active must be %d or %d.',
                    Status::ACTIVE->value,
                    Status::INACTIVE->value
                )
            );
        }
    }
}