<?php

namespace SuperVMar\Product\Domain\ValueObject;

use SuperVMar\Shared\Domain\Exception\InvalidValueException;
use SuperVMar\Shared\Domain\ValueObject\FloatValueObject;

final readonly class Percent extends FloatValueObject
{
    public function __construct(float $value)
    {
        $this->validate($value);
        parent::__construct($value);
    }

    protected function validate(float $value): void
    {
        if ($value < 0) {
            throw new InvalidValueException('Percent must be positive float.');
        }
    }

    public function toStringPercent(): string
    {
        return $this->value . "%";
    }
}