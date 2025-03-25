<?php

namespace SuperVMar\Supermarket\Domain\Entity;

use SuperVMar\Supermarket\Domain\ValueObject\Coord;
use SuperVMar\Supermarket\Domain\ValueObject\Id;
use SuperVMar\Supermarket\Domain\ValueObject\Spots;

final readonly class Space
{
    public function __construct(
        private Id    $id,
        private Coord $position,
        private Spots $maxSpots
    ){}

    public function id(): Id
    {
        return $this->id;
    }

    public function position(): Coord
    {
        return $this->position;
    }

    public function maxSpots(): Spots
    {
        return $this->maxSpots;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            new Id($data['id']),
            Coord::fromArray($data['position']),
            new Spots($data['maxSpots'])
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->value(),
            'position' => $this->position->toArray(),
            'maxSpots' => $this->maxSpots->value()
        ];
    }

    public function equals(self $other): bool
    {
        return $this->id->equals($other->id);
    }

    public function compare(self $other): bool
    {
        return $this->maxSpots->equals($other->maxSpots);
    }
}