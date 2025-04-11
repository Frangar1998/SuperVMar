<?php

namespace SuperVMar\Product\Domain\Entity;

use SuperVMar\Product\Domain\ValueObject\Percent;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Domain\ValueObject\Name;

final readonly class Tax
{
    public function __construct(
        private Id      $id,
        private Name    $name,
        private Percent $percent
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

    public function percent(): Percent
    {
        return $this->percent;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            new Id($data['id']),
            new Name($data['name']),
            new Percent($data['percent'])
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->value(),
            'name' => $this->name->value(),
            'percent' => $this->percent->value()
        ];
    }

    public function equals(self $other): bool
    {
        return $this->id->equals($other->id());
    }
}