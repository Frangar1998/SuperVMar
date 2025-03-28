<?php

namespace SuperVMar\Supermarket\Domain\Entity;

use SuperVMar\Shared\Domain\ValueObject\Id;
use SuperVMar\Supermarket\Domain\Exception\InvalidZoneCoordinatesException;
use SuperVMar\Supermarket\Domain\ValueObject\Coord;
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

    /**
     * @throws InvalidZoneCoordinatesException
     */
    public static function fromArray(array $data): self
    {
        $zone = new self(
            new Id($data['id']),
            new Name($data['name']),
            Coord::fromArray($data['cornerTopLeft']),
            Coord::fromArray($data['cornerTopRight']),
            Coord::fromArray($data['cornerBottomLeft']),
            Coord::fromArray($data['cornerBottomRight']),
            Spaces::fromArray($data['spaces'])
        );
        if (!$zone->validateCoords()) {
            throw new InvalidZoneCoordinatesException($zone->cornerTopLeft, $zone->cornerTopRight, $zone->cornerBottomLeft, $zone->cornerBottomRight);
        }
        return $zone;
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

    public function validateCoords(): bool
    {
        return $this->cornerTopLeft->x()->value() < $this->cornerTopRight->x()->value()
            && $this->cornerTopLeft->x()->value() < $this->cornerBottomRight->x()->value()
            && $this->cornerTopLeft->y()->value() > $this->cornerBottomRight->y()->value()
            && $this->cornerTopLeft->y()->value() > $this->cornerBottomLeft->y()->value()
            && $this->cornerTopRight->y()->value() > $this->cornerBottomRight->y()->value()
            && $this->cornerTopRight->y()->value() > $this->cornerBottomLeft->y()->value()
            && $this->cornerTopRight->x()->value() > $this->cornerBottomLeft->x()->value()
            && $this->cornerBottomRight->x()->value() > $this->cornerBottomLeft->x()->value();
    }

    public function equals(self $other): bool
    {
        return $this->id->value() === $other->id->value();
    }

    public function compare(self $other): bool
    {
        $this->compareAndChangeSpaces($other->spaces());

        return $this->name->equals($other->name())
            && $this->cornerTopLeft->equals($other->cornerTopLeft())
            && $this->cornerTopRight->equals($other->cornerTopRight())
            && $this->cornerBottomRight->equals($other->cornerBottomRight())
            && $this->cornerBottomLeft->equals($other->cornerBottomLeft());

    }

    protected function compareAndChangeSpaces(Spaces $other): void
    {
        foreach ($this->spaces as $space) {
            if ($other->find($space) === null) {
                //TODO: Check if space has allocated products before removing it. Throw exception if yes.
                $this->spaces->remove($space);
            }
        }
        foreach ($other as $otherSpace) {
            $spaceKey = $this->spaces->find($otherSpace);
            if ($spaceKey !== null) {
                //TODO: Check if space has allocated products before replacing it. If exceed max throw exception else replace.
                $this->spaces->replace($otherSpace, $spaceKey);
            } else {
                $this->spaces->add($otherSpace);
            }
        }
    }

}