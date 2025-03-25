<?php

namespace SuperVMar\Shared\Domain\ValueObject;

use Ramsey\Uuid\Uuid as BaseUuid;
use SuperVMar\Shared\Domain\Exception\InvalidUuidValueException;

class Uuid extends StringValueObject
{
    public function __construct(protected string $value)
    {
        $this->validate($value);
        parent::__construct($value);
    }

    public static function random(): self
    {
        return new self(BaseUuid::uuid7()->toString());
    }

    /**
     * @throws InvalidUuidValueException
     */
    protected function validate(string $value): void
    {
        if (!BaseUuid::isValid($value)) {
            throw new InvalidUuidValueException($value);
        }
    }
}