<?php

namespace SuperVMar\Supermarket\Domain\Entity;

use SuperVMar\Supermarket\Domain\ValueObject\Coord;
use SuperVMar\Supermarket\Domain\ValueObject\Id;

final readonly class Space
{
    public function __construct(
        private Id $id,
        private Coord $position
    ){}

    public function id(): Id
    {
        return $this->id;
    }

    public function position(): Coord
    {
        return $this->position;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            new Id($data['id']),
            Coord::fromJson($data['position']),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->value(),
            'position' => $this->position->toArray()
        ];
    }
}