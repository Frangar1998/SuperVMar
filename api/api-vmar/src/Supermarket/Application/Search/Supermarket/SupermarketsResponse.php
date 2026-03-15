<?php

namespace SuperVMar\Supermarket\Application\Search\Supermarket;

use SuperVMar\Shared\Domain\Bus\Query\Response;

final readonly class SupermarketsResponse implements Response
{
    public function __construct(
        private array $supermarkets,
    )
    {
    }

    public function toArray(): array
    {
        return $this->supermarkets;
    }
}

