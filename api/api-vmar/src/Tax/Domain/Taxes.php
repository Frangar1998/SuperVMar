<?php

namespace SuperVMar\Tax\Domain;

use SuperVMar\Shared\Domain\Collection;

final class Taxes extends Collection
{
    protected function type(): string
    {
        return Tax::class;
    }

    public static function fromArray(array $taxes): self
    {
        return new self(
            array_map(
                fn(array $tax) => Tax::fromArray($tax),
                $taxes
            )
        );
    }
}