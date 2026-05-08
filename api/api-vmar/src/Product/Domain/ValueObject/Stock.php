<?php

namespace SuperVMar\Product\Domain\ValueObject;

use SuperVMar\Shared\Domain\Exception\InvalidValueException;
use SuperVMar\Shared\Domain\ValueObject\IntValueObject;

final readonly class Stock extends IntValueObject
{
    public function __construct(int $value)
    {
        $this->validate($value);
        parent::__construct($value);
    }

    protected function validate(int $value): void
    {
        if ($value < 0) {
            throw new InvalidValueException('Product stock must be positive integer.');
        }
    }

    public function add(self $addedStock): self
    {
        return new self($this->value + $addedStock->value());
    }

    public function subtract(self $subtractedStock): self
    {
        return new self(
            max($this->value - $subtractedStock->value(), 0)
        );
    }
}