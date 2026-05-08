<?php

namespace SuperVMar\Product\Domain\Entity;

use SuperVMar\Shared\Domain\Collection;

final class PriceHistory extends Collection
{
    protected function type(): string
    {
        return HistoricalPrice::class;
    }

    public static function fromArray(array $priceHistory): self
    {
        return new self(
            array_map(
                fn(array $historicalPrice) => HistoricalPrice::fromArray($historicalPrice),
                $priceHistory
            )
        );
    }
}