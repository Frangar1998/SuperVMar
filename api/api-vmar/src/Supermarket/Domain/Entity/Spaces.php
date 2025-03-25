<?php

namespace SuperVMar\Supermarket\Domain\Entity;

use SuperVMar\Shared\Domain\Collection;

final class Spaces extends Collection
{
    protected function type(): string
    {
        return Space::class;
    }

    public static function fromArray(array $spaces): self
    {
        return new self(
            array_map(
                fn(array $space) => Space::fromArray($space),
                $spaces
            )
        );
    }
}