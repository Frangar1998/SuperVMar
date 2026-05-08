<?php

namespace SuperVMar\Supermarket\Application\Search\Zones;

use SuperVMar\Shared\Domain\Bus\Query\Response;

final readonly class ZonesResponse implements Response
{
    public function __construct(
        private array $zones
    )
    {
    }

    public function toArray(): array
    {
        return [
            'zones' => $this->zones
        ];
    }
}