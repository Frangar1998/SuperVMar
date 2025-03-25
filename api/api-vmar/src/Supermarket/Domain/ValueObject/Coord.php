<?php

namespace SuperVMar\Supermarket\Domain\ValueObject;

final readonly class Coord
{
    public function __construct(
        private Point $x,
        private Point $y,
        private ?Point $z = null
    ){}

    public function x(): Point
    {
        return $this->x;
    }

    public function y(): Point
    {
        return $this->y;
    }

    public function z(): Point
    {
        return $this->z;
    }

    public function isSpace(): bool
    {
        return isset($this->z);
    }

    public function toArray(): array
    {
        return $this->isSpace() ?
            [
                'x' => $this->x->value(),
                'y' => $this->y->value(),
                'z' => $this->z->value()
            ]
            :
            [
                'x' => $this->x->value(),
                'y' => $this->y->value()
            ];
    }

    public static function fromJson(string $jsonCoord): self
    {
        $coord = json_decode($jsonCoord, true);
        return new self(
            new Point($coord['x']),
            new Point($coord['y']),
            isset($coord['z']) ? new Point($coord['z']) : null
        );
    }

    public static function fromArray(array $coord): self
    {
        return new self(
            new Point($coord['x']),
            new Point($coord['y']),
            isset($coord['z']) ? new Point($coord['z']) : null
        );
    }

    public function equals(self $other): bool
    {
        return $this->x->equals($other->x())
            && $this->y->equals($other->y())
            && (!isset($this->z) || $this->z()->equals($other->z()));
    }

    public function __toString(): string
    {
        return $this->isSpace() ?
            sprintf(
                '{"x": %d, "y": %d, "z": %d}',
                $this->x->value(),
                $this->y->value(),
                $this->z->value()
            )
            :
            sprintf(
                '{"x": %d, "y": %d}',
                $this->x->value(),
                $this->y->value()
            );
    }
}