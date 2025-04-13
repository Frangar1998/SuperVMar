<?php

namespace SuperVMar\ProductAllocation\Domain\Entity;

use SuperVMar\ProductAllocation\Domain\ValueObject\Coord;
use SuperVMar\ProductAllocation\Domain\ValueObject\Spots;
use SuperVMar\Shared\Domain\ValueObject\Id;

final readonly class Space
{
    public function __construct(
        private Id    $id,
        private ?Coord $position = null,
        private ?Spots $maxSpots = null,
        private ?Zone  $zone = null
    ){}

    public function id(): Id
    {
        return $this->id;
    }

    public function position(): ?Coord
    {
        return $this->position;
    }

    public function maxSpots(): ?Spots
    {
        return $this->maxSpots;
    }

    public function zone(): ?Zone
    {
        return $this->zone;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            new Id($data['id']),
            !empty($data['position']) ? Coord::fromArray($data['position']) : null,
            !empty($data['maxSpots']) ? new Spots($data['maxSpots']) : null,
            !empty($data['zone']) ? Zone::fromArray($data['zone']) : null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->value(),
            'position' => $this->position->toArray(),
            'maxSpots' => $this->maxSpots->value(),
            'zone' => $this->zone->toArray()
        ];
    }

    public function equals(self $other): bool
    {
        return $this->id->equals($other->id);
    }
}