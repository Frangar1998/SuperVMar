<?php

namespace SuperVMar\Tax\Domain;

use SuperVMar\Shared\Domain\AggregateRoot;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Domain\ValueObject\Name;
use SuperVMar\Tax\Domain\ValueObject\Percent;

final class Tax extends AggregateRoot
{
    public function __construct(
        private readonly Id $id,
        private Name       $name,
        private Percent    $percent
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

    public function changeName(Name $name): void
    {
        if (!$this->name->equals($name)) {
            $this->name = $name;
        }
    }

    public function changePercent(Percent $percent): void
    {
        if (!$this->percent->equals($percent)) {
            $this->percent = $percent;
        }
    }

    public static function create(
        Id      $id,
        Name    $name,
        Percent $percent
    ): self
    {
        return new self(
            $id,
            $name,
            $percent
        );
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
}