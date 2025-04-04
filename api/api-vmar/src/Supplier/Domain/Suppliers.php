<?php

namespace SuperVMar\Supplier\Domain;

use SuperVMar\Shared\Domain\Collection;

final class Suppliers extends Collection
{
    protected function type(): string
    {
        return Supplier::class;
    }

    public static function fromArray(array $suppliers): self
    {
        return new self(
            array_map(
                fn(array $supplier) => Supplier::fromArray($supplier),
                $suppliers
            )
        );
    }
}