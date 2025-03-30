<?php

namespace SuperVMar\User\Domain\Entity;

use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\User\Domain\ValueObject\Name;

final readonly class Supermarket
{
    public function __construct(
        private Id   $id,
        private ?Name $name = null
    )
    {
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function name(): ?Name
    {
        return $this->name;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            new Id($data['id']),
            $data['name'] != null ? new Name($data['name']) : $data['name']
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->value(),
            'name' => $this->name?->value()
        ];
    }
}