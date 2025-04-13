<?php

namespace SuperVMar\Supermarket\Application\Search\Zones;

use SuperVMar\Shared\Domain\Bus\Query\Query;

final readonly class SearchZonesQuery implements Query
{
    public function __construct(
        private string $idSupermarket
    )
    {
    }

    public function idSupermarket(): string
    {
        return $this->idSupermarket;
    }
}