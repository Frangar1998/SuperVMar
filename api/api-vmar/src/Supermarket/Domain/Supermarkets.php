<?php

namespace SuperVMar\Supermarket\Domain;

use SuperVMar\Shared\Domain\Collection;

final class Supermarkets extends Collection
{
    protected function type(): string
    {
        return Supermarket::class;
    }

    public static function fromArray(array $supermarkets): self
    {
        return new self(
            array_map(
                fn(array $supermarket) => Supermarket::fromArray($supermarket),
                $supermarkets
            )
        );
    }
}

