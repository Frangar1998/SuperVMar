<?php

namespace SuperVMar\Shared\Domain\ValueObject;

use DateTime;
use SuperVMar\Shared\Domain\Exception\InvalidValueException;

abstract readonly class DateValueObject
{
    protected DateTime $value;

    public function __construct(string $value = '')
    {
        if (empty($value)) {
            $value = 'now';
        }
        $this->value = date_create($value);
    }

    public function __toString(): string
    {
        return $this->format('Y-m-d H:i:s');
    }

    final public function equals(self $otherValue): bool
    {
        return $this->value === $otherValue->value();
    }

    final public function value(): DateTime
    {
        return $this->value;
    }

    final public function format(string $format): string
    {
        return $this->value->format($format);
    }
}