<?php

namespace SuperVMar\User\Domain\Entity;

use SuperVMar\User\Domain\ValueObject\Id;
use SuperVMar\User\Domain\ValueObject\Name;

final readonly class UserJob
{
    public function __construct(
        private Id $id,
        private Name $name
    )
    {
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function name(): Name
    {
        return $this->name;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            new Id($data['id']),
            new Name($data['name'])
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->value(),
            'name' => $this->name->value()
        ];
    }

    public function equals(self $other): bool
    {
        return $this->id->equals($other->id());
    }
}