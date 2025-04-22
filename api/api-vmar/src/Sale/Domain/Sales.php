<?php

namespace SuperVMar\Sale\Domain;

use SuperVMar\Shared\Domain\Collection;

final class Sales extends Collection
{
    protected function type(): string
    {
        return Sale::class;
    }

    public static function fromArray(array $sales): self
    {
        return new self(
            array_map(
                fn(array $sale) => Sale::fromArray($sale),
                $sales
            )
        );
    }
}