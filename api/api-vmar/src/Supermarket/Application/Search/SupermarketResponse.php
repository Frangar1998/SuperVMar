<?php

namespace SuperVMar\Supermarket\Application\Search;

use SuperVMar\Shared\Domain\Bus\Query\Response;

final readonly class SupermarketResponse implements Response
{
    public function __construct(
        private string $id,
        private string $name,
        private array $address,
        private string $phone,
        private array $zones
    )
    {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone,
            'zones' => $this->zones
        ];
    }
}