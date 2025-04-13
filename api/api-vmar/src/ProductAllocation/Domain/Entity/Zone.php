<?php

namespace SuperVMar\ProductAllocation\Domain\Entity;

use SuperVMar\ProductAllocation\Domain\ValueObject\Coord;
use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Shared\Domain\ValueObject\Name;

final readonly class Zone
{
    public function __construct(
        private Id     $id,
        private Name   $name,
        private Coord  $cornerTopLeft,
        private Coord  $cornerTopRight,
        private Coord  $cornerBottomLeft,
        private Coord  $cornerBottomRight,
    ){}

    public function id(): Id
    {
        return $this->id;
    }

    public function name(): Name
    {
        return $this->name;
    }

    public function cornerTopLeft(): Coord
    {
        return $this->cornerTopLeft;
    }

    public function cornerTopRight(): Coord
    {
        return $this->cornerTopRight;
    }

    public function cornerBottomLeft(): Coord
    {
        return $this->cornerBottomLeft;
    }

    public function cornerBottomRight(): Coord
    {
        return $this->cornerBottomRight;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            new Id($data['id']),
            new Name($data['name']),
            Coord::fromArray($data['cornerTopLeft']),
            Coord::fromArray($data['cornerTopRight']),
            Coord::fromArray($data['cornerBottomLeft']),
            Coord::fromArray($data['cornerBottomRight']),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->value(),
            'name' => $this->name->value(),
            'cornerTopLeft' => $this->cornerTopLeft->toArray(),
            'cornerTopRight' => $this->cornerTopRight->toArray(),
            'cornerBottomLeft' => $this->cornerBottomLeft->toArray(),
            'cornerBottomRight' => $this->cornerBottomRight->toArray(),
        ];
    }

    public function equals(self $other): bool
    {
        return $this->id->equals($other->id());
    }

}