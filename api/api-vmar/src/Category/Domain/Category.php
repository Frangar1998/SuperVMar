<?php

namespace SuperVMar\Category\Domain;

use SuperVMar\Category\Domain\ValueObject\Name;
use SuperVMar\Shared\Domain\AggregateRoot;
use SuperVMar\Shared\Domain\ValueObject\Id;

final class Category extends AggregateRoot
{
    public function __construct(
        private readonly Id $id,
        private Name       $name
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

    public function changeName(Name $name): void
    {
        if (!$this->name->equals($name)) {
            $this->name = $name;
        }
    }

    public static function create(
        Id   $id,
        Name $name,
    ): self
    {
        return new self(
            $id,
            $name
        );
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
}