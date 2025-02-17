<?php

namespace SuperVMar\Supermarket\Domain\Entity;

use SuperVMar\Supermarket\Domain\ValueObject\Coord;
use SuperVMar\Supermarket\Domain\ValueObject\Id;
use SuperVMar\Supermarket\Domain\ValueObject\Name;

final readonly class Zone
{
    public function __construct(
        private Id     $id,
        private Name   $name,
        private Coord  $cornerTopLeft,
        private Coord  $cornerTopRight,
        private Coord  $cornerBottomLeft,
        private Coord  $cornerBottomRight,
        private Spaces $spaces,
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

    public function spaces(): Spaces
    {
        return $this->spaces;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            new Id($data['id']),
            new Name($data['name']),
            Coord::fromJson($data['cornerTopLeft']),
            Coord::fromJson($data['cornerTopRight']),
            Coord::fromJson($data['cornerBottomLeft']),
            Coord::fromJson($data['cornerBottomRight']),
            new Spaces(
                array_map(
                    fn(array $space) => Space::fromArray($space),
                    $data['spaces']
                )
            )
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
            'spaces' => $this->spaces->toArray(),
        ];
    }
}