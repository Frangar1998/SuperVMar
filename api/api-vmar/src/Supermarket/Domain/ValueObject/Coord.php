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
            $coord['x'],
            $coord['y'],
            $coord['z'] ?? null
        );
    }

}