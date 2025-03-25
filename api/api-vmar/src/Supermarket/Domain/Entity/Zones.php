<?php

namespace SuperVMar\Supermarket\Domain\Entity;

use SuperVMar\Shared\Domain\Collection;

final class Zones extends Collection
{
    protected function type(): string
    {
        return Zone::class;
    }

    public static function fromArray(array $zones): self
    {
        return new self(
            array_map(
                fn(array $zone) => Zone::fromArray($zone),
                $zones
            )
        );
    }
}