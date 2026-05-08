<?php

namespace SuperVMar\User\Domain\Entity;

use SuperVMar\Shared\Domain\Collection;

final class Allocations extends Collection
{

    protected function type(): string
    {
        return Allocation::class;
    }

    public static function fromArray(array $allocations): self
    {
        return new self(
            array_map(
                fn(array $allocation) => Allocation::fromArray($allocation),
                $allocations
            )
        );
    }
}